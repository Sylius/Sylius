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

namespace Sylius\Component\Channel\Model;

use Doctrine\Common\Collections\Collection;

interface ChannelsAwareInterface
{
    /**
     * @return Collection|ChannelInterface[]
     */
    public function getChannels(): Collection;

    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannel(ChannelInterface $channel): bool;

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel): void;

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel): void;
}
