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

use Laminas\Stdlib\PriorityQueue;
use Sylius\Component\Order\Model\OrderInterface;

final class CompositeCartContext implements CartContextInterface
{
    /** @var PriorityQueue<CartContextInterface> */
    private PriorityQueue $cartContexts;

    public function __construct()
    {
        $this->cartContexts = new PriorityQueue();
    }

    public function addContext(CartContextInterface $cartContext, int $priority = 0): void
    {
        $this->cartContexts->insert($cartContext, $priority);
    }

    public function getCart(): OrderInterface
    {
        foreach ($this->cartContexts as $cartContext) {
            try {
                return $cartContext->getCart();
            } catch (CartNotFoundException) {
                continue;
            }
        }

        throw new CartNotFoundException();
    }
}
