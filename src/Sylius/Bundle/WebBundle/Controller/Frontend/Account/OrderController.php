<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Account order controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class OrderController extends FOSRestController
{
    /**
     * List orders of the current customer.
     *
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (null === $user) {
            throw $this->createAccessDeniedException();
        }

        $orders = $this->getOrderRepository()->findByCustomer($user->getCustomer(), ['updatedAt' => 'desc']);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Order/index.html.twig')
            ->setData([
                'orders' => $orders,
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * Get single order of the current customer.
     *
     * @param string $number
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showAction($number)
    {
        $order = $this->findOrderOr404($number);
        $this->checkAccessToOrder($order);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Order/show.html.twig')
            ->setData([
                'order' => $order,
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * Renders an invoice as PDF.
     *
     * @param Request $request
     * @param string  $number
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function renderInvoiceAction(Request $request, $number)
    {
        $order = $this->findOrderOr404($number);
        $this->checkAccessToOrder($order);

        if (!$order->isInvoiceAvailable()) {
            throw $this->createNotFoundException('The invoice can not yet be generated.');
        }

        $html = $this->renderView('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig', [
            'order' => $order,
        ]);

        $generator = $this
            ->get('knp_snappy.pdf')
            ->getInternalGenerator();

        $generator->setOptions([
            'footer-left' => '[title]',
            'footer-right' => '[page]/[topage]',
            'footer-line' => true,
            'footer-font-name' => '"Helvetica Neue",​Helvetica,​Arial,​sans-serif',
            'footer-font-size' => 10,
            'viewport-size' => '1200x1024',
            'margin-top' => 10,
            'margin-right' => 5,
            'margin-bottom' => 10,
            'margin-left' => 5,
        ]);

        return new Response(
            $generator->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$order->getNumber().'.pdf"',
            ]
        );
    }

    /**
     * @param string $number
     *
     * @return Response
     */
    public function showPaymentsAction($number)
    {
        $order = $this->findOrderOr404($number);
        $this->checkAccessToOrder($order);

        if ($order->getLastPayment(PaymentInterface::STATE_COMPLETED)) {
            return $this->redirectToRoute('sylius_checkout_thank_you', ['id' => $order->getId()]);
        }

        $this->get('sylius.order_processing.payment_processor')->processOrderPayments($order);
        $this->getOrderManager()->flush();

        return $this->render('SyliusWebBundle:Frontend/Account/Order:showPayments.html.twig', ['order' => $order]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function afterPurchaseAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new GetStatus($token);
        $this->getPayum()->getGateway($token->getGatewayName())->execute($status);
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();
        $this->checkAccessToOrder($order);

        $orderStateResolver = $this->get('sylius.order_processing.state_resolver');
        $orderStateResolver->resolvePaymentState($order);
        $orderStateResolver->resolveShippingState($order);

        $this->getOrderManager()->flush();
        if ($status->isCanceled() || $status->isFailed()) {
            return $this->redirectToRoute('sylius_order_payment_index', ['number' => $order->getNumber()]);
        }

        return $this->redirectToRoute('sylius_checkout_thank_you');
    }

    /**
     * @param mixed $paymentId
     *
     * @return Response
     */
    public function purchaseAction($paymentId)
    {
        $paymentRepository = $this->get('sylius.repository.payment');
        $payment = $paymentRepository->find($paymentId);

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            'sylius_order_after_purchase'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }

    /**
     * @param string $number
     *
     * @return OrderInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOrderOr404($number)
    {
        /* @var $order OrderInterface */
        if (null === $order = $this->getOrderRepository()->findOneByNumber($number)) {
            throw $this->createNotFoundException('The order does not exist.');
        }

        return $order;
    }

    /**
     * @param OrderInterface $order
     *
     * @throws AccessDeniedException
     */
    protected function checkAccessToOrder(OrderInterface $order)
    {
        $customerGuestId = $this->get('session')->get('sylius_customer_guest_id');
        $customerGuest = null;

        if (null !== $customerGuestId) {
            $customerGuest = $this->get('sylius.repository.customer')->find($customerGuestId);
        }

        $loggedInCustomer = $this->getCustomer();
        $expectedCustomer = $order->getCustomer();

        if ($expectedCustomer !== $customerGuest && $expectedCustomer !== $loggedInCustomer) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @return null|CustomerInterface
     */
    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    protected function getHttpRequestVerifier()
    {
        return $this->get('payum.security.http_request_verifier');
    }

    /**
     * @param OrderInterface $order
     *
     * @return FormInterface
     */
    protected function createCheckoutPaymentForm(OrderInterface $order)
    {
        return $this->createForm('sylius_checkout_payment', $order);
    }

    /**
     * @return EntityManager
     */
    protected function getOrderManager()
    {
        return $this->get('sylius.manager.order');
    }
}
