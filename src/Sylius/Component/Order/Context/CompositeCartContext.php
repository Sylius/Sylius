<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Context;

use Zend\Stdlib\PriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
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
    public function addContext(CartContextInterface $cartContext, $priority = 0)
    {
        $this->cartContexts->insert($cartContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
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
