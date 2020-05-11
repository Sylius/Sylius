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

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\SalesSummaryProviderInterface;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var SalesSummaryProviderInterface */
    private $salesSummaryProvider;

    public function __construct(SalesSummaryProviderInterface $salesSummaryProvider)
    {
        $this->salesSummaryProvider = $salesSummaryProvider;
    }

    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummaryInterface
    {
        $startDate = (new \DateTime('first day of next month last year'));
        $startDate->setTime(0, 0, 0);
        $endDate = (new \DateTime('last day of this month'));
        $endDate->setTime(23, 59, 59);

        $data = $this->salesSummaryProvider->getSalesSummary($channel, $startDate, $endDate);

        return new SalesSummary(
            $startDate,
            $endDate,
            $data
        );
    }
}
