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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Resolver\SalesSummaryProviderResolver;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var SalesSummaryProviderResolver */
    private $salesSummaryProviderResolver;

    /** @var IntervalsConverterInterface */
    private $intervalsConverter;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        SalesSummaryProviderResolver $salesSummaryProviderResolver,
        IntervalsConverterInterface $intervalsConverter,
        EntityManagerInterface $entityManager
    ) {
        $this->salesSummaryProviderResolver = $salesSummaryProviderResolver;
        $this->intervalsConverter = $intervalsConverter;
        $this->entityManager = $entityManager;
    }

    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummaryInterface
    {
    function getSalesSummary(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $interval,
        ChannelInterface $channel,
        string $dateFormat
    ): SalesSummaryInterface {
        $provider = $this->salesSummaryProviderResolver->getSalesSummaryProvider($this->entityManager->getConnection());

        return new SalesSummary(
            $this->intervalsConverter->getIntervals($startDate, $endDate, $interval),
            $provider->getSalesSummary($channel, $startDate, $endDate, $interval),
            $dateFormat
        );
    }
}
