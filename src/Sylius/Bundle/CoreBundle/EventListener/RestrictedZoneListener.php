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

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RestrictedZoneListener
{
    private $zoneMatcher;
    private $securityContext;
    private $session;
    private $translator;

    public function __construct(ZoneMatcherInterface $zoneMatcher, SecurityContextInterface $securityContext, Session $session, TranslatorInterface $translator)
    {
        $this->zoneMatcher = $zoneMatcher;
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function handleRestrictedZone(GenericEvent $event)
    {
        $order = $event->getSubject();

        $address = $this->securityContext->getToken()->getUser()->getShippingAddress();
        if (null === $address) {
            return;
        }

        foreach ($order->getItems() as $item) {
            if (in_array($item->getProduct()->getRestrictedZone(), $this->zoneMatcher->matchAll($address))) {
                $order->removeItem($item);

                $message = $this->translator->trans('sylius.checkout.restricted_zone', array(), 'flashes');
                $this->session->getFlashBag()->add('error', $message);
            }
        }
    }
}
