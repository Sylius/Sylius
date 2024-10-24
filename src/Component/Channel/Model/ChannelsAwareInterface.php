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

namespace Sylius\Component\Channel\Model;

use Doctrine\Common\Collections\Collection;

interface ChannelsAwareInterface
{
    /**
     * @return Collection<array-key, ChannelInterface>
     */
    public function getChannels(): Collection;

    public function hasChannel(ChannelInterface $channel): bool;

    public function addChannel(ChannelInterface $channel): void;

    public function removeChannel(ChannelInterface $channel): void;
}
