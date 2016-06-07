<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Context;

use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartContext implements CartContextInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $providersRegistry;

    /**
     * @param PrioritizedServiceRegistryInterface $providersRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $providersRegistry)
    {
        $this->providersRegistry = $providersRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        foreach ($this->providersRegistry->all() as $cartProvider) {
            $cart = $cartProvider->getCart();

            if (null !== $cart) {
                return $cart;
            }
        }

        throw new CartNotFoundException();
    }
}
