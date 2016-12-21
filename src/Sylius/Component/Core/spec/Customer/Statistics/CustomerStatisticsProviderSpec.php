<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Customer\Statistics;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProvider;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerStatisticsProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, RepositoryInterface $channelRepository)
    {
        $this->beConstructedWith($orderRepository, $channelRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerStatisticsProvider::class);
    }

    function it_implements_customer_statistics_provider_interface()
    {
        $this->shouldImplement(CustomerStatisticsProviderInterface::class);
    }

    function it_returns_an_empty_statistic_if_given_customer_does_not_have_any_orders(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $channelRepository,
        ChannelInterface $channel,
        CustomerInterface $customer
    ) {
        $expectedStatistics = new CustomerStatistics([]);

        $channelRepository->findAll()->willReturn([$channel]);
        $orderRepository->findByCustomer($customer)->willReturn([]);

        $this->getCustomerStatistics($customer)->shouldBeLike($expectedStatistics);
    }

    function it_obtains_customer_statistics_from_a_single_channel(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ChannelInterface $channelWithoutOrders,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        CustomerInterface $customer
    ) {
        $firstOrder->getChannel()->willReturn($channel);
        $secondOrder->getChannel()->willReturn($channel);

        $firstOrder->getTotal()->willReturn(10000);
        $secondOrder->getTotal()->willReturn(23000);

        $expectedStatistics = new CustomerStatistics([
            new PerChannelCustomerStatistics(2, 33000, $channel->getWrappedObject())
        ]);

        $channelRepository->findAll()->willReturn([$channel, $channelWithoutOrders]);
        $orderRepository->findByCustomer($customer)->willReturn([$firstOrder, $secondOrder]);

        $this->getCustomerStatistics($customer)->shouldBeLike($expectedStatistics);
    }

    function it_obtains_customer_statistics_from_multiple_channels(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $channelRepository,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder,
        OrderInterface $fourthOrder,
        OrderInterface $fifthOrder,
        CustomerInterface $customer
    ) {
        $allOrders = [$firstOrder, $secondOrder, $thirdOrder, $fourthOrder, $fifthOrder];

        $firstOrder->getChannel()->willReturn($firstChannel);
        $secondOrder->getChannel()->willReturn($firstChannel);

        $firstOrder->getTotal()->willReturn(10000);
        $secondOrder->getTotal()->willReturn(23000);

        $thirdOrder->getChannel()->willReturn($secondChannel);
        $fourthOrder->getChannel()->willReturn($secondChannel);
        $fifthOrder->getChannel()->willReturn($secondChannel);

        $thirdOrder->getTotal()->willReturn(2000);
        $fourthOrder->getTotal()->willReturn(8000);
        $fifthOrder->getTotal()->willReturn(1000);

        $expectedStatistics = new CustomerStatistics([
            new PerChannelCustomerStatistics(2, 33000, $firstChannel->getWrappedObject()),
            new PerChannelCustomerStatistics(3, 11000, $secondChannel->getWrappedObject())
        ]);

        $channelRepository->findAll()->willReturn([$firstChannel, $secondChannel]);
        $orderRepository->findByCustomer($customer)->willReturn($allOrders);

        $this->getCustomerStatistics($customer)->shouldBeLike($expectedStatistics);
    }
}
