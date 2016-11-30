<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $hostname
     *
     * @return ChannelInterface|null
     */
    public function findOneByHostname($hostname);

    /**
     * @param string $code
     *
     * @return ChannelInterface|null
     */
    public function findOneByCode($code);

    /**
     * @param string $name
     *
     * @return ChannelInterface[]
     */
    public function findByName($name);
}
