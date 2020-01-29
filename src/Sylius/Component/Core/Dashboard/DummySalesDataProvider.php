<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;

final class DummySalesDataProvider implements SalesDataProviderInterface
{
    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummary
    {
        return new SalesSummary([
            '02.19' => 399,
            '03.19' => 1200,
            '04.19' => 199,
            '05.19' => 788,
            '06.19' => 399,
            '07.19' => 1100,
            '08.19' => 199,
            '09.19' => 23,
            '10.19' => 399,
            '11.19' => 999,
            '12.19' => 199,
            '01.20' => 567,
        ]);
    }
}
