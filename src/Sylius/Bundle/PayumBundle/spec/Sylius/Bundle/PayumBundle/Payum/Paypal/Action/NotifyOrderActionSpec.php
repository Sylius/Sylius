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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Payum\Core\PaymentInterface;
use Payum\Core\Request\ModelRequestInterface;
use Payum\Core\Request\SecuredNotifyRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface as PaymentModelInterface;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotifyOrderActionSpec extends ObjectBehavior
{
    function let(
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $objectManager,
        FactoryInterface $factory,
        PaymentInterface $payment
    ) {
        $this->beConstructedWith($eventDispatcher, $objectManager, $factory);
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
        PaymentModelInterface $payment
    ) {
        $request->getModel()->willReturn($payment);

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
        $factory,
        SecuredNotifyRequest $request,
        OrderInterface $order,
        PaymentModelInterface $paymentModel,
        PaymentInterface $payment,
        StateMachineInterface $sm,
        Collection $payments
    ) {
        $request->getModel()->willReturn($paymentModel);
        $order->getPayments()->willReturn($payments);
        $payments->last()->willReturn($payment);

        $paymentModel->getState()->willReturn(Payment::STATE_COMPLETED);

        $factory->get($paymentModel, PaymentTransitions::GRAPH)->willReturn($sm);
        $sm->getTransitionToState('completed')->willReturn(null);
        $sm->apply(PaymentTransitions::SYLIUS_COMPLETE)->shouldNotBeCalled();

        $payment->execute(Argument::type('Payum\Core\Request\SyncRequest'))->willReturn(null);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) {
                $args[0]->markSuccess();
            }
        );

        $this->execute($request);
    }

    function it_must_dispatch_pre_and_post_payment_state_changed_if_state_changed(
        $factory,
        SecuredNotifyRequest $request,
        OrderInterface $order,
        PaymentModelInterface $paymentModel,
        PaymentInterface $payment,
        ObjectManager $objectManager,
        StateMachineInterface $sm,
        Collection $payments
    ) {
        $request->getModel()->willReturn($paymentModel);
        $order->getPayments()->willReturn($payments);
        $payments->last()->willReturn($payment);

        $paymentModel->getState()->willReturn(Payment::STATE_PENDING);

        $factory->get($paymentModel, PaymentTransitions::GRAPH)->willReturn($sm);
        $sm->getTransitionToState('cancelled')->willReturn(PaymentTransitions::SYLIUS_CANCEL);
        $sm->apply(PaymentTransitions::SYLIUS_CANCEL)->shouldBeCalled()->will(function ($args) use ($paymentModel) {
            $paymentModel->getState()->willReturn(Payment::STATE_CANCELLED);
        });

        $payment->execute(Argument::type('Payum\Core\Request\SyncRequest'))->willReturn(null);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest'))
            ->will(function ($args) {
                $args[0]->markCanceled();
            }
        );

        $objectManager->flush()->shouldBeCalled();

        $this->execute($request);
    }
}
