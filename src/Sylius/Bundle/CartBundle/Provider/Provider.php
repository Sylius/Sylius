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

use Symfony\Component\DependencyInjection\ContainerAware;
use Sylius\Bundle\CartBundle\Model\CartInterface;

/**
 * Container for cart.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Provider extends ContainerAware
{
    protected $cart;

    public function getCart()
    {
        if (null == $this->cart) {
            $cartManager = $this->container->get('sylius_cart.manager.cart');
            $session = $this->container->get('request')->getSession();

            $cartId = $session->get('sylius_cart.id', false);

            if ($cartId) {
                $cart = $cartManager->findCart($cartId);

                if ($cart) {
                    $this->cart = $cart;
                    return $cart;
                }
            }

            $cart = $cartManager->createCart();
            $cartManager->persistCart($cart);
            $session->set('sylius_cart.id', $cart->getId());

            $this->cart = $cart;
        }

        return $this->cart;
    }
}
