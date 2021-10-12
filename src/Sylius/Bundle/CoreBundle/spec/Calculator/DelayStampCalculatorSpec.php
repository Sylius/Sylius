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

namespace spec\Sylius\Bundle\CoreBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Bundle\CoreBundle\Exception\TargetTimeSmallerException;

class DelayStampCalculatorSpec extends ObjectBehavior
{
    function it_implements_delay_stamp_calculator_interface(): void
    {
        $this->shouldImplement(DelayStampCalculatorInterface::class);
    }

    function it_calculates_delay_stamp_from_given_dates(): void
    {
        $currentTime = new \DateTime('@1634083200');
        $targetTime = new \DateTime('@1634085200');

        $this->calculate($currentTime, $targetTime)->shouldReturn(200000);
    }

    function it_throws_error_if_dates_target_time_is_smaller_than_current_time(): void
    {
        $currentTime = new \DateTime('@1634083200');
        $targetTime = new \DateTime('@1634000000');

        $this->shouldThrow(TargetTimeSmallerException::class)->during('calculate', [$currentTime, $targetTime]);
    }
}
