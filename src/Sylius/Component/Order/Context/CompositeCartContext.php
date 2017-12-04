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

namespace Sylius\Component\Order\Context;

use Sylius\Component\Order\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

final class CompositeCartContext implements CartContextInterface
{
    /**
     * @var PriorityQueue|CartContextInterface[]
     */
    private $cartContexts;

    public function __construct()
    {
        $this->cartContexts = new PriorityQueue();
    }

    /**
     * @param CartContextInterface $cartContext
     * @param int $priority
     */
    public function addContext(CartContextInterface $cartContext, int $priority = 0): void
    {
        $this->cartContexts->insert($cartContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(): OrderInterface
    {
        foreach ($this->cartContexts as $cartContext) {
            try {
                return $cartContext->getCart();
            } catch (CartNotFoundException $exception) {
                continue;
            }
        }

        throw new CartNotFoundException();
    }
}
