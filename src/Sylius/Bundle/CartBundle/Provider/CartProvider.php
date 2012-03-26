<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Provider;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\CartManagerInterface;
use Sylius\Bundle\CartBundle\Storage\CartStorageInterface;

/**
 * Container for cart.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartProvider implements CartProviderInterface
{
    /**
     * Cart identifier storage.
     *
     * @var CartStorageInterface
     */
    protected $storage;

    /**
     * Cart manager.
     *
     * @var CartManagerInterface
     */
    protected $cartManager;

    /**
     * Cart.
     *
     * @var CartInterface
     */
    protected $cart;

    /**
     * Constructor.
     *
     * @param CartStorageInterface $storage
     * @param CartManagerInterface $cartManager
     */
    public function __construct(CartStorageInterface $storage, CartManagerInterface $cartManager)
    {
        $this->storage = $storage;
        $this->cartManager = $cartManager;
    }

    /**
     * Returns current cart or creates a new one.
     *
     * @return CartInterface
     */
    public function getCart()
    {
        if (null == $this->cart) {
            $cartIdentifier = $this->storage->getCurrentCartIdentifier();

            if ($cartIdentifier) {
                $cart = $this->cartManager->findCart($cartIdentifier);

                if ($cart) {
                    $this->cart = $cart;
                    return $cart;
                }
            }

            $cart = $this->cartManager->createCart();
            $this->cartManager->persistCart($cart);
            $this->storage->setCurrentCartIdentifier($cart->getId());

            $this->cart = $cart;
        }

        return $this->cart;
    }

    /**
     * Sets current cart.
     *
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }
}
