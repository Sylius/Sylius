<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class DelayStampCalculatorSpec extends ObjectBehavior
{
    function it_implements_delay_stamp_calculator_interface(): void
    {
        $this->shouldImplement(DelayStampCalculatorInterface::class);
    }

    function it_calculates_delay_stamp_from_given_dates(): void
    {
        $currentTime = new \DateTime('2021-11-11 20:20');
        $targetTime = new \DateTime('2021-11-11 20:21');

        $delayStamp = new DelayStamp(60000);

        $this->calculate($currentTime, $targetTime)->shouldBeLike($delayStamp);
    }

    function it_returns_0_delay_if_dates_target_time_is_smaller_than_current_time(): void
    {
        $currentTime = new \DateTime('2021-11-11 20:21');
        $targetTime = new \DateTime('2021-11-11 19:05');

        $delayStamp = new DelayStamp(0);

        $this->calculate($currentTime, $targetTime)->shouldBeLike($delayStamp);
    }
}
