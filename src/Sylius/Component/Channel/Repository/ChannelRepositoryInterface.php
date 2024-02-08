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

interface ChannelRepositoryInterface extends RepositoryInterface
{
    /** @deprecated since Sylius 1.11, use the `findOneEnabledByHostname` method instead */
    public function findOneByHostname(string $hostname): ?ChannelInterface;

    public function findOneEnabledByHostname(string $hostname): ?ChannelInterface;

    public function findOneByCode(string $code): ?ChannelInterface;

    /**
     * @return iterable|ChannelInterface[]
     */
    public function findByName(string $name): iterable;
}
