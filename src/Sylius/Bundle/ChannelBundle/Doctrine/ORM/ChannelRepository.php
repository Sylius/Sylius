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
        return $this->findOneBy(['hostname' => $hostname], self::ORDER_BY);
    }

    public function findOneEnabledByHostname(string $hostname): ?ChannelInterface
    {
        return $this->findOneBy(['hostname' => $hostname, 'enabled' => true], self::ORDER_BY);
    }

    public function findOneByCode(string $code): ?ChannelInterface
    {
        return $this->findOneBy(['code' => $code], self::ORDER_BY);
    }

    public function findByName(string $name): iterable
    {
        return $this->findBy(['name' => $name], self::ORDER_BY);
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
