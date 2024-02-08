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

namespace Sylius\Component\Order\Context;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CartContext implements CartContextInterface
{
    public function __construct(private FactoryInterface $cartFactory)
    {
    }

    public function getCart(): OrderInterface
    {
        return $this->cartFactory->createNew();
    }
}
