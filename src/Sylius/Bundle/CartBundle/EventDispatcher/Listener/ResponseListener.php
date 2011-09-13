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

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Response listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResponseListener extends ContainerAware
{
    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if (!$event->getRequest()->isXmlHttpRequest()) {
                $cart = $this->container->get('sylius_cart.provider')->getCart();
                
                if ($cart) {
                    $event->getResponse()->headers->setCookie(new Cookie('SYLIUS_CART_HASH', $cart->getHash(), $cart->getExpiresAt()->getTimestamp()));
                }
            }
        }
    }
}
