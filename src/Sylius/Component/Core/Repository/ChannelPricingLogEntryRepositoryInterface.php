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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ChannelPricingLogEntryInterface
 * @extends RepositoryInterface<T>
 */
interface ChannelPricingLogEntryRepositoryInterface extends RepositoryInterface
{
    public function createByChannelPricingIdListQueryBuilder(mixed $channelPricingId): QueryBuilder;

    public function findLatestOneByChannelPricing(ChannelPricingInterface $channelPricing): ?ChannelPricingLogEntryInterface;

    public function findLowestPriceInPeriod(
        int $latestChannelPricingLogEntryId,
        ChannelPricingInterface $channelPricing,
        \DateTimeInterface $startDate,
    ): ?int;

    /**
     * @return array|ChannelPricingLogEntryInterface[]
     */
    public function findOlderThan(\DateTimeInterface $date, ?int $limit = null): array;
}
