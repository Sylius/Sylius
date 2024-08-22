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

namespace Sylius\Bundle\ChannelBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of ChannelInterface
 *
 * @implements ChannelRepositoryInterface<T>
 */
class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    private const ORDER_BY = ['id' => 'ASC'];

    public function findOneByHostname(string $hostname): ?ChannelInterface
    {
        $channel = $this->findOneBy(['hostname' => $hostname], self::ORDER_BY);
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }

    public function findOneEnabledByHostname(string $hostname): ?ChannelInterface
    {
        $channel = $this->findOneBy(['hostname' => $hostname, 'enabled' => true], self::ORDER_BY);
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }

    public function findOneByCode(string $code): ?ChannelInterface
    {
        $channel = $this->findOneBy(['code' => $code], self::ORDER_BY);
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }

    public function findByName(string $name): iterable
    {
        $channels = $this->findBy(['name' => $name], self::ORDER_BY);
        Assert::allIsInstanceOf($channels, ChannelInterface::class);

        return $channels;
    }

    public function findAllWithBasicData(): iterable
    {
        return $this->createQueryBuilder('o')
            ->select(['o.code', 'o.name', 'o.hostname'])
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
    }

    /**
     * @return ChannelInterface[]
     */
    public function findEnabled(): iterable
    {
        /** @var ChannelInterface[] $enabledChannels */
        $enabledChannels = $this->findBy(['enabled' => true]);

        return $enabledChannels;
    }

    public function countAll(): int
    {
        return $this->count([]);
    }
}
