<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Calculator\FeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DelegatingFeeCalculatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $serviceRegistry)
    {
        $this->beConstructedWith($serviceRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Payment\Calculator\DelegatingFeeCalculator');
    }

    function it_implements_delegating_fee_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface');
    }

    function it_delegates_calculation_to_proper_calculator(
        PaymentSubjectInterface $payment,
        PaymentMethodInterface $paymentMethod,
        FeeCalculatorInterface $feeCalculator,
        $serviceRegistry
    ) {
        $payment->getMethod()->willReturn($paymentMethod)->shouldBeCalled();

        $paymentMethod->getFeeCalculator()->willReturn('fee_calculator')->shouldBeCalled();
        $paymentMethod->getFeeCalculatorConfiguration()->willReturn(array('amount' => 150))->shouldBeCalled();

        $serviceRegistry->get('fee_calculator')->willReturn($feeCalculator)->shouldBeCalled();

        $feeCalculator->calculate($payment, array('amount' => 150))->willReturn(150)->shouldBeCalled();

        $this->calculate($payment)->shouldReturn(150);
    }

    function it_throws_exception_if_passed_payment_has_no_payment_method_defined(PaymentSubjectInterface $payment)
    {
        $payment->getMethod()->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException("Cannot calculate fee for payment without payment method configured."))->during("calculate", array($payment));
    }
}
