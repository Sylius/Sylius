<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\PayumBundle\Payum\Paypal\Action;

use Payum\Core\PaymentInterface;
use Payum\Core\Request\ModelRequestInterface;
use Payum\Core\Request\SecuredNotifyRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\Payment;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface as PaymentModelInterface;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotifyOrderActionSpec extends ObjectBehavior
{
    function let(
        EventDispatcherInterface $eventDispatcher,
        PaymentInterface $payment
    ) {
        $this->beConstructedWith($eventDispatcher);
        $this->setPayment($payment);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Paypal\Action\NotifyOrderAction');
    }

    function it_extends_payum_payment_aware_action()
    {
        $this->shouldHaveType('Payum\Core\Action\PaymentAwareAction');
    }

    function it_should_supports_secured_notify_request_with_order_model(
        SecuredNotifyRequest $request,
        OrderInterface $order
    ) {
        $request->getModel()->willReturn($order);

        $this->supports($request)->shouldReturn(true);
    }

    function it_should_support_only_secured_request(ModelRequestInterface $request)
    {
        $this->supports($request)->shouldReturn(false);
    }

    function it_should_not_support_notify_request_with_not_payment_model(SecuredNotifyRequest $request)
    {
        $request->getModel()->willReturn(new \stdClass);

        $this->supports($request)->shouldReturn(false);
    }

    function it_should_not_support_anything_not_model_request()
    {
        $this->supports(new \stdClass)->shouldReturn(false);
    }

    function it_throws_exception_if_executing_not_supported_request()
    {
        $this
            ->shouldThrow('Payum\Core\Exception\RequestNotSupportedException')
            ->duringExecute($notSupportedRequest = 'foo')
        ;
    }

    function it_must_not_dispatch_pre_and_post_payment_state_changed_if_state_not_changed(
        SecuredNotifyRequest $request,
        OrderInterface $order,
        PaymentModelInterface $paymentModel,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher
    ) {
        $request->getModel()->willReturn($order);
        $order->getPayment()->willReturn($paymentModel);

        $paymentModel->getState()->willReturn(Payment::STATE_COMPLETED);
        $paymentModel->setState(Argument::type('string'))->will(function($args) use ($paymentModel) {
            $paymentModel->getState()->willReturn($args[0]);
        });

        $payment->execute(Argument::type('Payum\Core\Request\SyncRequest'))->willReturn(null);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) {
                $args[0]->markSuccess();
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

        $this->execute($request);
    }

    function it_must_dispatch_pre_and_post_payment_state_changed_if_state_changed(
        SecuredNotifyRequest $request,
        OrderInterface $order,
        PaymentModelInterface $paymentModel,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher
    ) {
        $request->getModel()->willReturn($order);
        $order->getPayment()->willReturn($paymentModel);

        $paymentModel->getState()->willReturn(Payment::STATE_COMPLETED);
        $paymentModel->setState(Argument::type('string'))->will(function($args) use ($paymentModel) {
            $paymentModel->getState()->willReturn($args[0]);
        });

        $payment->execute(Argument::type('Payum\Core\Request\SyncRequest'))->willReturn(null);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) {
                $args[0]->markCanceled();
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

        $this->execute($request);
    }
}
