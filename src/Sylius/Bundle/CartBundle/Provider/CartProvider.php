<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartsBundle\Provider;

use Sylius\Bundle\CartsBundle\Model\CartInterface;
use Sylius\Bundle\CartsBundle\Model\CartManagerInterface;
use Sylius\Bundle\CartsBundle\Storage\CartStorageInterface;

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
            $cartManager = $this->container->get('sylius_cart.manager.cart');
            $session = $this->container->get('request')->getSession();

            $cartId = $this->storage->getCurrentCartId();

            if ($cartId) {
                $cart = $cartManager->findCart($cartId);

                if ($cart) {
                    $this->cart = $cart;
                    return $cart;
                }
            }

            $cart = $cartManager->createCart();
            $cartManager->persistCart($cart);
            $this->storage->setCurrentCartId($cart->getId());

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
