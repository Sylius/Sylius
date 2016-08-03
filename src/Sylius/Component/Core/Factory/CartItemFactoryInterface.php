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

use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface CartItemFactoryInterface extends FactoryInterface
{
    /**
     * @param mixed $id
     *
     * @return CartItemInterface
     */
    public function createForProductWithId($id);
}
