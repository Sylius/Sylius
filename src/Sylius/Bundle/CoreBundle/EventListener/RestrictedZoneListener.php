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
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RestrictedZoneListener
{
    /**
     * @var RestrictedZoneCheckerInterface
     */
    private $restrictedZoneChecker;
    /**
     * @var CartProviderInterface
     */
    private $cartProvider;
    /**
     * @var ObjectManager
     */
    private $cartManager;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RestrictedZoneCheckerInterface $restrictedZoneChecker
     * @param CartProviderInterface          $cartProvider
     * @param ObjectManager                  $cartManager
     * @param SessionInterface               $session
     * @param TranslatorInterface            $translator
     */
    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker, CartProviderInterface $cartProvider, ObjectManager $cartManager, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
        $this->cartProvider = $cartProvider;
        $this->cartManager = $cartManager;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param GenericEvent $event
     */
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
