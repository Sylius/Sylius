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

namespace Sylius\Bundle\OrderBundle\Controller;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToCartCommand implements AddToCartCommandInterface
{
    /** @var OrderInterface */
    private $cart;

    /** @var OrderItemInterface */
    private $cartItem;

    public function __construct(OrderInterface $cart, OrderItemInterface $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    public function getCart(): OrderInterface
    {
        return $this->cart;
    }

    public function getCartItem(): OrderItemInterface
    {
        return $this->cartItem;
    }
}
