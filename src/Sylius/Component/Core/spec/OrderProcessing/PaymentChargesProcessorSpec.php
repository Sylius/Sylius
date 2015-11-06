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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentChargesProcessorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $adjustmentRepository,
        DelegatingFeeCalculatorInterface $delegatingFeeCalculator,
        OriginatorInterface $originator
    ) {
        $this->beConstructedWith($adjustmentRepository, $delegatingFeeCalculator, $originator);
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
        $adjustmentRepository,
        $delegatingFeeCalculator,
        $originator,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        PaymentSubjectInterface $payment,
        PaymentMethodInterface $paymentMethod
    ) {
        $order->getPayments()->willReturn(array($payment));
        $order->getAdjustments(AdjustmentInterface::PROMOTION_ADJUSTMENT)->willReturn(array());

        $originator->getOrigin($adjustment)->willReturn(null);

        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getName()->willReturn('testPaymentMethod');

        $delegatingFeeCalculator->calculate($payment)->willReturn(50);

        $adjustmentRepository->createNew()->willReturn($adjustment);
        $adjustment->setLabel('payment')->shouldBeCalled();
        $adjustment->setAmount(50)->shouldBeCalled();
        $adjustment->setDescription('testPaymentMethod')->shouldBeCalled();

        $originator->setOrigin($adjustment, $payment)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();

        $order->calculateTotal()->shouldBeCalled();

        $this->applyPaymentCharges($order);
    }
}
