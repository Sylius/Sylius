<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\PayumBundle\Checkout\Step;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Symfony\Bridge\Doctrine\RegistryInterface as DoctrinRegistryInterface;
use Payum\Core\PaymentInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Model\Order;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\PaymentsBundle\Model\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class PurchaseStepSpec extends ObjectBehavior
{
    function let(
        ContainerInterface $container,
        ProcessContextInterface $context,
        HttpRequestVerifierInterface $httpRequestVerifier,
        TokenInterface $token,
        Request $request,
        CartProviderInterface $cartProvider,
        RegistryInterface $payum,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        DoctrinRegistryInterface $doctrine,
        ObjectManager $objectManager,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $session->getFlashBag()->willReturn($flashBag);
        $doctrine->getManager()->willReturn($objectManager);
        $token->getPaymentName()->willReturn('aPaymentName');
        $payum->getPayment('aPaymentName')->willReturn($payment);
        $httpRequestVerifier->verify($request)->willReturn($token);
        $httpRequestVerifier->invalidate($token)->willReturn(null);

        $container->get('payum.security.http_request_verifier')->willReturn($httpRequestVerifier);
        $container->get('request')->willReturn($request);
        $container->get('sylius.cart_provider')->willReturn($cartProvider);
        $container->get('payum')->willReturn($payum);
        $container->get('event_dispatcher')->willReturn($eventDispatcher);
        $container->get('session')->willReturn($session);
        $container->get('doctrine')->willReturn($doctrine);
        $container->has('doctrine')->willReturn(true);
        $container->get('translator')->willReturn($translator);

        $this->setName('purchase');

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Checkout\Step\PurchaseStep');
    }

    function it_extends_checkout_step()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Checkout\Step\CheckoutStep');
    }

    function it_must_dispatch_pre_and_post_payment_state_changed_if_state_changed(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_NEW);
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markSuccess();
                $args[0]->setModel($order);
            }
        );

        $eventDispatcher
            ->dispatch(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;
        $eventDispatcher
            ->dispatch(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;
        $eventDispatcher
            ->dispatch(
                'sylius_checkout_purchase.completed',
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $cartProvider->getCart()->shouldBeCalled()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_must_not_dispatch_pre_and_post_payment_state_changed_if_state_not_changed(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_COMPLETED);
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markSuccess();
                $args[0]->setModel($order);
            }
        );

        $eventDispatcher
            ->dispatch(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldNotBeCalled()
        ;
        $eventDispatcher
            ->dispatch(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldNotBeCalled()
        ;
        $eventDispatcher
            ->dispatch(
                'sylius_checkout_purchase.completed',
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $cartProvider->getCart()->shouldBeCalled()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_success_flash_message_if_payment_status_success(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markSuccess();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.success', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.success')
        ;
        $flashBag->add('success','translated.sylius.checkout.success')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_notice_flash_message_if_payment_status_pending(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markPending();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.pending', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.pending')
        ;
        $flashBag->add('notice','translated.sylius.checkout.pending')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_notice_flash_message_if_payment_status_canceled(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markCanceled();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.canceled', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.canceled')
        ;
        $flashBag->add('notice','translated.sylius.checkout.canceled')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_error_flash_message_if_payment_status_expired(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markExpired();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.failed', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.failed')
        ;
        $flashBag->add('error','translated.sylius.checkout.failed')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_notice_flash_message_if_payment_status_suspended(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markSuspended();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.canceled', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.canceled')
        ;
        $flashBag->add('notice','translated.sylius.checkout.canceled')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_error_flash_message_if_payment_status_failed(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markFailed();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.failed', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.failed')
        ;
        $flashBag->add('error','translated.sylius.checkout.failed')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_should_set_error_flash_message_if_payment_status_unknown(
        ProcessContextInterface $context,
        PaymentInterface $payment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartProviderInterface $cartProvider
    ) {
        $paymentModel = new Payment();
        $order = new Order();
        $order->setPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markUnknown();
                $args[0]->setModel($order);
            }
        );

        $translator
            ->trans('sylius.checkout.unknown', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.unknown')
        ;
        $flashBag->add('error','translated.sylius.checkout.unknown')->shouldBeCalled();

        $cartProvider->getCart()->willReturn($order);
        $cartProvider->abandonCart()->shouldBeCalled();

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }
}
