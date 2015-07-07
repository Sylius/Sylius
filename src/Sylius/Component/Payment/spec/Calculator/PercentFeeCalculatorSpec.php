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
use Sylius\Component\Payment\Model\PaymentSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PercentFeeCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Payment\Calculator\PercentFeeCalculator');
    }

    function it_implement_fee_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Calculator\FeeCalculatorInterface');
    }

    function it_calculates_fee_for_given_payment_with_given_configuration(PaymentSubjectInterface $payment)
    {
        $payment->getAmount()->willReturn(1000);

        $this->calculate($payment, array('percent' => 20))->shouldReturn(200);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('percent');
    }
}
