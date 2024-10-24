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

namespace spec\Sylius\Component\Core\Customer\Statistics;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;

final class CustomerStatisticsSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([]);
    }

    function it_throws_an_exception_when_array_does_not_contain_only_per_channel_statistics(): void
    {
        $this->beConstructedWith([new \DateTime()]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_returns_zero_if_there_are_no_per_channel_statistics(): void
    {
        $this->getAllOrdersCount()->shouldReturn(0);
    }

    function it_has_number_of_all_orders(ChannelInterface $channel): void
    {
        $firstStatistics = new PerChannelCustomerStatistics(110, 120, $channel->getWrappedObject());
        $secondStatistics = new PerChannelCustomerStatistics(13, 120, $channel->getWrappedObject());

        $this->beConstructedWith([$firstStatistics, $secondStatistics]);

        $this->getAllOrdersCount()->shouldReturn(123);
    }

    function it_has_an_array_of_statistics_per_channel(ChannelInterface $channel): void
    {
        $firstStatistics = new PerChannelCustomerStatistics(110, 120, $channel->getWrappedObject());
        $secondStatistics = new PerChannelCustomerStatistics(13, 120, $channel->getWrappedObject());

        $this->beConstructedWith([$firstStatistics, $secondStatistics]);

        $this->getPerChannelsStatistics()->shouldReturn([$firstStatistics, $secondStatistics]);
    }
}
