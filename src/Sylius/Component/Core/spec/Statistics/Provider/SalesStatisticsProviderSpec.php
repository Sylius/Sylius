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

namespace spec\Sylius\Component\Core\Statistics\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProviderRegistryInterface;

final class SalesStatisticsProviderSpec extends ObjectBehavior
{
    function let(OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry): void
    {
        $this->beConstructedWith($ordersTotalsProviderRegistry, [
            'day' => [
                'interval' => 'P1D',
                'period_format' => 'Y-m-d',
            ],
        ]);
    }

    function it_is_a_sales_statistics_provider(): void
    {
        $this->shouldImplement(SalesStatisticsProviderInterface::class);
    }

    function it_throws_an_exception_when_period_format_for_interval_type_is_unknown(
        OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        \DatePeriod $datePeriod,
        ChannelInterface $channel,
    ): void {
        $ordersTotalsProviderRegistry->getByType('dummy')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', ['dummy', $datePeriod, $channel])
        ;
    }

    function it_throws_an_exception_when_interval_type_is_unknown(
        OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        \DatePeriod $datePeriod,
        ChannelInterface $channel,
    ): void {
        $ordersTotalsProviderRegistry
            ->getByType('day')
            ->willThrow(\InvalidArgumentException::class)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', ['day', $datePeriod, $channel])
        ;
    }

    function it_returns_an_empty_array_when_no_statistics_are_available_in_period(
        OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        OrdersTotalsProviderInterface $provider,
        \DatePeriod $datePeriod,
        ChannelInterface $channel,
    ): void {
        $provider->provideForPeriodInChannel($datePeriod, $channel)->willReturn([]);

        $ordersTotalsProviderRegistry->getByType('day')->willReturn($provider);

        $this->provide('day', $datePeriod, $channel)->shouldReturn([]);
    }

    function it_returns_statistics(
        OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        OrdersTotalsProviderInterface $provider,
        \DatePeriod $datePeriod,
        ChannelInterface $channel,
    ): void {
        $provider->provideForPeriodInChannel($datePeriod, $channel)->willReturn([
            [
                'period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-01'),
                'total' => 1000,
            ],
            [
                'period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-02'),
                'total' => 2000,
            ],
            [
                'period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-03'),
                'total' => 2000,
            ],
        ]);

        $ordersTotalsProviderRegistry->getByType('day')->willReturn($provider);

        $this->provide('day', $datePeriod, $channel)->shouldReturn([
            [
                'period' => '1999-01-01',
                'total' => 1000,
            ],
            [
                'period' => '1999-01-02',
                'total' => 2000,
            ],
            [
                'period' => '1999-01-03',
                'total' => 2000,
            ],
        ]);
    }
}
