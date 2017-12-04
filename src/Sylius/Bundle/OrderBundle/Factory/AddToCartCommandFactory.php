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

namespace Sylius\Bundle\OrderBundle\Factory;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToCartCommandFactory implements AddToCartCommandFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createWithCartAndCartItem(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartCommandInterface
    {
        return new AddToCartCommand($cart, $cartItem);
    }
}
