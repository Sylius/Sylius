<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface CartItemFactoryInterface extends FactoryInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return OrderItemInterface
     */
    public function createForProduct(ProductInterface $product);
}
