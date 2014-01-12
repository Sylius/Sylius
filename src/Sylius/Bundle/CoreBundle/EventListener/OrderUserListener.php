<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Common\Persistence\ObjectManager;

class OrderUserListener
{
    protected $cartProvider;
    protected $cartManager;

    public function __construct(CartProviderInterface $cartProvider, ObjectManager $cartManager)
    {
        $this->cartProvider = $cartProvider;
        $this->cartManager = $cartManager;
    }

    public function setOrderUser(InteractiveLoginEvent $event)
    {
        $cart = $this->cartProvider->getCart();
        $cart->setUser($event->getAuthenticationToken()->getUser());

        $this->cartManager->persist($cart);
        $this->cartManager->flush($cart);
    }
}
