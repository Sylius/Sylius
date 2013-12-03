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

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;

class RestrictedZoneListener
{
    private $restrictedZoneChecker;
    private $cartProvider;
    private $cartManager;
    private $session;
    private $translator;

    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker, CartProviderInterface $cartProvider, ObjectManager $cartManager, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
        $this->cartProvider = $cartProvider;
        $this->cartManager = $cartManager;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function handleRestrictedZone(GenericEvent $event)
    {
        $removed = false;
        $cart = $this->cartProvider->getCart();

        foreach ($cart->getItems() as $item) {
            if ($this->restrictedZoneChecker->isRestricted($product = $item->getProduct(), $cart->getShippingAddress())) {
                $cart->removeItem($item);
                $removed = true;

                $this->session->getBag('flashes')->add(
                    'error',
                    $this->translator->trans('sylius.cart.restricted_zone_removal', array('%product%' => $product->getName()), 'flashes')
                );
            }
        }

        if ($removed) {
            $cart->calculateTotal();

            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        }
    }
}
