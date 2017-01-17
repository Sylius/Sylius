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

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelsAwareInterface
{
    /**
     * @return Collection|ChannelInterface[]
     */
    public function getChannels();

    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel);
}
