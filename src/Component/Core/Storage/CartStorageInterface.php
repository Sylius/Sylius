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

namespace Sylius\Component\Core\Storage;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CartStorageInterface
{
    public function hasForChannel(ChannelInterface $channel): bool;

    public function getForChannel(ChannelInterface $channel): ?OrderInterface;

    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void;

    public function removeForChannel(ChannelInterface $channel): void;
}
