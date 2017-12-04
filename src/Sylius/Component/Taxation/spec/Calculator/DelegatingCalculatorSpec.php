<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxation\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class DelegatingCalculatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $calculators, CalculatorInterface $calculator): void
    {
        $calculators->get('default')->willReturn($calculator);

        $this->beConstructedWith($calculators);
    }

    function it_is_a_calculator(): void
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_should_delegate_calculation_to_a_correct_calculator(
        CalculatorInterface $calculator,
        TaxRateInterface $rate
    ): void {
        $rate->getCalculator()->willReturn('default');

        $calculator->calculate(100, $rate)->shouldBeCalled()->willReturn(23.00);

        $this->calculate(100, $rate)->shouldReturn(23.00);
    }
}
