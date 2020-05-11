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

namespace Sylius\Component\Core\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Resolver\SalesSummaryProviderResolver;

class SalesSummaryProvider implements SalesSummaryProviderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SalesSummaryProviderResolver */
    private $salesSummaryProviderResolver;

    public function __construct(EntityManagerInterface $entityManager, SalesSummaryProviderResolver $salesSummaryProviderResolver)
    {
        $this->entityManager = $entityManager;
        $this->salesSummaryProviderResolver = $salesSummaryProviderResolver;
    }

    public function getSalesSummary(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $provider = $this->salesSummaryProviderResolver->getSalesSummaryProvider($this->entityManager->getConnection());

        return $provider->getSalesSummary($channel, $startDate, $endDate);
    }
}
