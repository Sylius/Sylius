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

namespace Sylius\Component\Channel\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ChannelInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ChannelRepositoryInterface extends RepositoryInterface
{
    /** @deprecated since Sylius 1.11, use the `findOneEnabledByHostname` method instead */
    public function findOneByHostname(string $hostname): ?ChannelInterface;

    public function findOneEnabledByHostname(string $hostname): ?ChannelInterface;

    public function findOneByCode(string $code): ?ChannelInterface;

    /** @return iterable|ChannelInterface[] */
    public function findByName(string $name): iterable;

    public function findAllWithBasicData(): iterable;

    /**
     * @return ChannelInterface[]
     */
    public function findEnabled(): iterable;

    public function countAll(): int;
}
