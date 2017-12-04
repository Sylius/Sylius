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

namespace Sylius\Component\Core\Storage;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CartStorageInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasForChannel(ChannelInterface $channel): bool;

    /**
     * @param ChannelInterface $channel
     *
     * @return OrderInterface|null
     */
    public function getForChannel(ChannelInterface $channel): ?OrderInterface;

    /**
     * @param ChannelInterface $channel
     * @param OrderInterface $cart
     */
    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void;

    /**
     * @param ChannelInterface $channel
     */
    public function removeForChannel(ChannelInterface $channel): void;
}
