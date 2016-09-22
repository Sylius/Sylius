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
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartItemEvent extends CartEvent
{
    /**
     * @var OrderItemInterface
     */
    private $item;

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    public function __construct(OrderInterface $cart, OrderItemInterface $item)
    {
        parent::__construct($cart);

        $this->item = $item;
        $this->subject = $item;
    }

    /**
     * @return OrderItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
