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

namespace Sylius\Component\Core\Customer\Statistics;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerStatisticsProvider implements CustomerStatisticsProviderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    public function __construct(OrderRepositoryInterface $orderRepository, RepositoryInterface $channelRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerStatistics(CustomerInterface $customer): CustomerStatistics
    {
        $orders = $this->orderRepository->findForCustomerStatistics($customer);
        if (empty($orders)) {
            return new CustomerStatistics([]);
        }

        $perChannelCustomerStatisticsArray = [];

        $channels = $this->channelRepository->findAll();
        foreach ($channels as $channel) {
            $channelOrders = $this->filterOrdersByChannel($orders, $channel);
            if (empty($channelOrders)) {
                continue;
            }

            $perChannelCustomerStatisticsArray[] = new PerChannelCustomerStatistics(
                count($channelOrders),
                $this->getOrdersSummedTotal($channelOrders),
                $channel
            );
        }

        return new CustomerStatistics($perChannelCustomerStatisticsArray);
    }

    /**
     * @param array|OrderInterface[] $orders
     */
    private function getOrdersSummedTotal(array $orders): int
    {
        return array_sum(
            array_map(function (OrderInterface $order) {
                return $order->getTotal();
            }, $orders)
        );
    }

    /**
     * @param array|OrderInterface[] $orders
     *
     * @return array|OrderInterface[]
     */
    private function filterOrdersByChannel(array $orders, ChannelInterface $channel): array
    {
        return array_filter($orders, function (OrderInterface $order) use ($channel) {
            return $order->getChannel() === $channel;
        });
    }
}
