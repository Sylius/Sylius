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

use Sylius\Component\Cart\Repository\CartRepositoryInterface as BaseCartRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartRepositoryInterface extends BaseCartRepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     * @param CustomerInterface $customer
     *
     * @return null|OrderInterface
     */
    public function findOneByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer);
}
