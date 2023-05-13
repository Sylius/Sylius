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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of OrderItemInterface
 * @extends FactoryInterface<T>
 */
interface CartItemFactoryInterface extends FactoryInterface
{
    public function createForProduct(ProductInterface $product): OrderItemInterface;

    public function createForCart(OrderInterface $order): OrderItemInterface;
}
