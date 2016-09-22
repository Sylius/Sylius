<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Event;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Event\ResourceEvent;

/**
 * Cart event.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartEvent extends ResourceEvent
{
    /**
     * @var OrderInterface
     */
    protected $cart;

    /**
     * @param OrderInterface $cart
     */
    public function __construct(OrderInterface $cart)
    {
        $this->subject = $cart;
        $this->cart = $cart;
    }

    /**
     * @return OrderInterface
     */
    public function getCart()
    {
        return $this->cart;
    }
}
