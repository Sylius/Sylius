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

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\IntervalsConverterInterface;

final class IntervalsConverterSpec extends ObjectBehavior
{
    function it_implements_a_intervals_converter_interface(): void
    {
        $this->shouldImplement(IntervalsConverterInterface::class);
    }

    function it_provides_date_period_with_supported_interval(): void
    {
        $this
            ->getIntervals(new \DateTime('yesterday'), new \DateTime('tomorrow'), 'hour')
            ->shouldBeLike(new \DatePeriod(new \DateTime('yesterday'), \DateInterval::createFromDateString('1 hour'), new \DateTime('tomorrow')))
        ;
    }

    function it_throws_exception_if_provide_not_supported_interval(): void
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('minute is a not supported interval'))
            ->during('getIntervals', [new \DateTime('yesterday'), new \DateTime(), 'minute'])
        ;
    }

    function it_throws_exception_if_end_date_is_earlier_than_start_date(): void
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('endDate should be later then startDate'))
            ->during('getIntervals', [new \DateTime(), new \DateTime('2 days ago'), 'month'])
        ;
    }
}
