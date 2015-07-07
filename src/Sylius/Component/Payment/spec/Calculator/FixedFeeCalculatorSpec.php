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
class FixedFeeCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Payment\Calculator\FixedFeeCalculator');
    }

    function it_implements_sylius_fee_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Calculator\FeeCalculatorInterface');
    }

    function it_calculates_fee(PaymentSubjectInterface $payment)
    {
        $this->calculate($payment, array('amount' => 15))->shouldReturn(15);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('fixed');
    }
}
