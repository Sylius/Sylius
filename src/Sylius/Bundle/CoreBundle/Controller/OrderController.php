<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Gedmo\Loggable\Entity\LogEntry;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\ResourceActions;
use Sylius\Component\User\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrderController extends ResourceController
{
    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByCustomerAction(Request $request, $id)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $customer = $this->container->get('sylius.repository.customer')->findForDetailsPage($id);

        if (!$customer) {
            throw new NotFoundHttpException('Requested customer does not exist.');
        }

        $paginator = $this->repository->createPaginatorByCustomer($customer, $configuration->getSorting());

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($configuration->getPaginationMaxPerPage());

        // Fetch and cache deleted orders
        $paginator->getCurrentPageResults();
        $paginator->getNbResults();

        return $this->container->get('templating')->renderResponse('SyliusWebBundle:Backend/Order:indexByCustomer.html.twig', [
            'customer' => $customer,
            'orders' => $paginator,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function releaseInventoryAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $order = $this->findOr404($configuration);

        $this->container->get('sm.factory')
            ->get($order, OrderTransitions::GRAPH)
            ->apply(OrderTransitions::SYLIUS_RELEASE)
        ;

        $this->manager->flush();

        return $this->redirectHandler->redirectToReferer($configuration);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var $order OrderInterface */
        $order = $this->findOr404($configuration);

        $repository = $this->get('doctrine')->getManager()->getRepository(LogEntry::class);

        $items = [];
        foreach ($order->getItems() as $item) {
            $items[] = $repository->getLogEntries($item);
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('history.html'))
            ->setData([
                'order' => $order,
                'logs' => [
                    'order' => $repository->getLogEntries($order),
                    'order_items' => $items,
                    'billing_address' => $repository->getLogEntries($order->getBillingAddress()),
                    'shipping_address' => $repository->getLogEntries($order->getShippingAddress()),
                ],
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     * @param int $orderId
     *
     * @return Response
     */
    public function payAction(Request $request, $orderId)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $order = $this->repository->findOneForPayment($orderId);

        $this->checkAccessToOrder($order);

        if (PaymentInterface::STATE_COMPLETED === $order->getPaymentState()) {
            return $this->redirectToRoute($configuration->getParameters()->get('redirect'));
        }

        $payment = $order->getLastPayment();
        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            $configuration->getParameters()->get('after_pay[route]', null, true),
            $configuration->getParameters()->get('after_pay[parameters]', [], true)
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function afterPayAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $token = $this->getHttpRequestVerifier()->verify($request);
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new GetStatus($token);
        $this->getPayum()->getGateway($token->getGatewayName())->execute($status);
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();

        $this->checkAccessToOrder($order);

        $orderStateResolver = $this->getOrderStateResolver();
        $orderStateResolver->resolvePaymentState($order);
        $orderStateResolver->resolveShippingState($order);

        if ($status->isCanceled() || $status->isFailed()) {
            return $this->redirectToRoute($configuration->getParameters()->get('canceled'));
        }

        $this->getOrderManager()->flush();

        return $this->redirectToRoute(
            $configuration->getParameters()->get('redirect[route]', null, true),
            $configuration->getParameters()->get('redirect[parameters]', [], true)
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function thankYouAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $this->getSession()->get('sylius_order_id');
        $order = $this->repository->findOneForPayment($orderId);

        return $this->render(
            $configuration->getParameters()->get('template'),
            ['order' => $order]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function afterCancelAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $this->getSession()->get('sylius_order_id');
        $order = $this->repository->findOneForPayment($orderId);

        return $this->render(
            $configuration->getParameters()->get('template'),
            ['order' => $order]
        );
    }

    /**
     * @param OrderInterface $order
     *
     * @throws AccessDeniedException
     */
    private function checkAccessToOrder(OrderInterface $order)
    {
        $customerGuest = $this->getCustomerGuest();

        $loggedInCustomer = $this->getCustomer();
        $expectedCustomer = $order->getCustomer();

        if ($expectedCustomer !== $customerGuest && $expectedCustomer !== $loggedInCustomer) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @return StateResolverInterface
     */
    private function getOrderStateResolver()
    {
        return $this->get('sylius.order_processing.state_resolver');
    }

    /**
     * @return SessionInterface
     */
    private function getSession()
    {
        return $this->get('session');
    }

    /**
     * @return null|CustomerInterface
     */
    private function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }

    /**
     * @return null|CustomerInterface
     */
    private function getCustomerGuest()
    {
        $customerGuestId = $this->get('session')->get('sylius_customer_guest_id');

        if (null !== $customerGuestId) {
            return $this->getCustomerRepository()->find($customerGuestId);
        }

        return null;
    }

    /**
     * @return CustomerRepositoryInterface
     */
    private function getCustomerRepository()
    {
        return $this->get('sylius.repository.customer');
    }

    /**
     * @return EntityManager
     */
    private function getOrderManager()
    {
        return $this->get('sylius.manager.order');
    }

    /**
     * @return RegistryInterface
     */
    private function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    private function getTokenFactory()
    {
        return $this->getPayum()->getTokenFactory();
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    private function getHttpRequestVerifier()
    {
        return $this->getPayum()->getHttpRequestVerifier();
    }
}
