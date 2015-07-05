<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Channel;

use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Interface for service defining the currently used channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelResolverInterface
{
    /**
     * Get currently used channel.
     *
     * @param string $hostname
     *
     * @return ChannelInterface
     */
    public function resolve($hostname = null);
}
