<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventDispatcher\Listener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Sylius\Bundle\CartBundle\Model\CartManagerInterface;

/**
 * Core request listener.
 * Creates cart for new user or maintains existing one.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RequestListener extends ContainerAware
{
    public function onCoreRequest(GetResponseEvent $event)
    {
        if(HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if(!$event->getRequest()->isXmlHttpRequest()) {
                $cartManager = $this->container->get('sylius_cart.manager.cart');

                $cookie = $this->container->get('request')->cookies->get('SYLIUS_CART_HASH');
                
                if (null === $cookie) {
                    $cart = $cartManager->createCart();
                    $this->container->get('sylius_cart.provider')->setCart($cart);
                    $cartManager->persistCart($cart);
                    return;
                }
                
                $cart = $cartManager->findCartBy(array('hash' => $cookie));
                
                if ($cart) {
                    $this->container->get('sylius_cart.provider')->setCart($cart);
                    return;
                }
                
                $cart = $cartManager->createCart();
                $this->container->get('sylius_cart.provider')->setCart($cart);
                $cartManager->persistCart($cart);
            }
        }
    }
}
