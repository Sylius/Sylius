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

use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RestrictedZoneListener
{
    private $restrictedZoneChecker;
    private $cartProvider;
    private $cartManager;
    private $session;
    private $translator;

    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker, CartProviderInterface $cartProvider, DomainManagerInterface $cartManager, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
        $this->cartProvider = $cartProvider;
        $this->cartManager = $cartManager;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function handleRestrictedZone(GenericEvent $event)
    {
        $cart = $event->getSubject();
        if (!$cart instanceof CartInterface) {
            $cart = $this->cartProvider->getCart();
        }

        $removed = false;
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

            $this->cartManager->update($cart);
        }
    }
}
