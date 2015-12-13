<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin \Sylius\Component\Core\OrderProcessing\PaymentChargesProcessor
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentChargesProcessorSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $eventDispatcher, DelegatingFeeCalculatorInterface $delegatingFeeCalculator)
    {
        $this->beConstructedWith($eventDispatcher, $delegatingFeeCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\PaymentChargesProcessor');
    }

    function it_implements_payment_charges_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\PaymentChargesProcessorInterface');
    }

    function it_applies_payment_charges(
        $delegatingFeeCalculator,
        EventDispatcherInterface $eventDispatcher,
        OrderInterface $order,
        PaymentSubjectInterface $payment,
        PaymentMethodInterface $paymentMethod
    ) {
        $order->removeAdjustments('payment')->shouldBeCalled();
        $order->getPayments()->willReturn(array($payment))->shouldBeCalled();

        $order->calculateTotal()->shouldBeCalled();

        $payment->getState()->willReturn('new')->shouldBeCalled();
        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getName()->willReturn('testPaymentMethod');

        $delegatingFeeCalculator->calculate($payment)->willReturn(50);

        $eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
            Argument::type(AdjustmentEvent::class)
        );

        $this->applyPaymentCharges($order);
    }
}
