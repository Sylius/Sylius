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
 * Interface implemented by objects related to multiple channels.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelsAwareInterface
{
    /**
     * @return Collection|ChannelInterface[]
     */
    public function getChannels();

    /**
     * @param Collection $collection
     */
    public function setChannels(Collection $collection);

    /**
     * @param ChannelInterface $channel
     *
     * @return Boolean
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
