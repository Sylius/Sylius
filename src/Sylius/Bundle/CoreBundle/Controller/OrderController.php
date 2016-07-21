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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

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
            ->apply(OrderTransitions::TRANSITION_RELEASE)
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
        Assert::notNull($order);

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

        $orderStateResolver = $this->getOrderStateResolver();
        $orderStateResolver->resolvePaymentState($order);
        $orderStateResolver->resolveShippingState($order);

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
        Assert::notNull($orderId);
        $order = $this->repository->findOneForPayment($orderId);
        Assert::notNull($order);

        $payment = $order->getLastPayment();
        if (null !== $payment && $payment->getMethod()->getGateway() === 'offline') {
            return $this->redirectToRoute('sylius_shop_order_pay', ['orderId' => $orderId]);
        }

        return $this->render($configuration->getParameters()->get('template'), ['order' => $order]);
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
