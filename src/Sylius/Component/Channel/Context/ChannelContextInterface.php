<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Provides the context of currently used channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelContextInterface
{
    /**
     * Get the currently active channel.
     *
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * Set the currently active channel.
     *
     * @param ChannelInterface $channel
     */
    public function setChannel(ChannelInterface $channel);
}
