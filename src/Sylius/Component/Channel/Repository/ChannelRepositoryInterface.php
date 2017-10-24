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

namespace Sylius\Component\Channel\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ChannelRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $hostname
     *
     * @return ChannelInterface|null
     */
    public function findOneByHostname(string $hostname): ?ChannelInterface;

    /**
     * @param string $code
     *
     * @return ChannelInterface|null
     */
    public function findOneByCode(string $code): ?ChannelInterface;

    /**
     * @param string $name
     *
     * @return iterable|ChannelInterface[]
     */
    public function findByName(string $name): iterable;
}
