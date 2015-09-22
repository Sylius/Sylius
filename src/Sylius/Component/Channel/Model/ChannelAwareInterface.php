<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Model;

/**
 * Interface implemented by models associated with a channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelAwareInterface
{
    /**
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * @param null|ChannelInterface $channel
     */
    public function setChannel(ChannelInterface $channel = null);
}
