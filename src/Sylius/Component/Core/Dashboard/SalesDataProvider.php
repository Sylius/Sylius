<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummary
    {
        $result = $this->orderRepository->getLastYearSalesPerMonthForChannel($channel);

        return new SalesSummary($result);
    }
}
