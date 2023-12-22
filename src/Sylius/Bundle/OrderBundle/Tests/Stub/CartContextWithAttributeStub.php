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

namespace Sylius\Bundle\OrderBundle\Tests\Stub;

use Sylius\Bundle\OrderBundle\Attribute\AsCartContext;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderInterface;

#[AsCartContext(priority: 20)]
final class CartContextWithAttributeStub implements CartContextInterface
{
    public function getCart(): OrderInterface
    {
        return new Order();
    }
}
