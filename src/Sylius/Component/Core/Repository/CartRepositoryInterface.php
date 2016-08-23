<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface as BaseCartRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface CartRepositoryInterface extends BaseCartRepositoryInterface
{
    /**
     * @param string $id
     * @param ChannelInterface $channel
     * 
     * @return null|CartInterface
     */
    public function findCartByIdAndChannel($id, ChannelInterface $channel);
}
