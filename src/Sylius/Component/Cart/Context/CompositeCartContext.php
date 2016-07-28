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
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeCartContext implements CartContextInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $cartContextsRegistry;

    /**
     * {@inheritdoc}
     */
    public function __construct(PrioritizedServiceRegistryInterface $cartContextsRegistry)
    {
        $this->cartContextsRegistry = $cartContextsRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        $cartContexts = $this->cartContextsRegistry->all();
        foreach ($cartContexts as $cartContext) {
            try {
                return $cartContext->getCart();
            } catch (CartNotFoundException $exception) {
                continue;
            }
        }

        throw new CartNotFoundException();
    }
}
