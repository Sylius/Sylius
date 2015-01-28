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

/**
 * Repository interface for channels.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelRepositoryInterface
{
    /**
     * Find channel best matching given hostname.
     *
     * @param string $hostname
     *
     * @return null|ChannelInterface
     */
    public function findMatchingHostname($hostname);
}
