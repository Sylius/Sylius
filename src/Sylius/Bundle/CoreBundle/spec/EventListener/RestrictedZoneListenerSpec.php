<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RestrictedZoneListenerSpec extends ObjectBehavior
{
    function let(
        RestrictedZoneCheckerInterface $restrictedZoneChecker,
        CartProviderInterface $cartProvider,
        ObjectManager $cartManager,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->beConstructedWith($restrictedZoneChecker, $cartProvider, $cartManager, $session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\RestrictedZoneListener');
    }

    function it_sets_cart_if_event_contains_invalid_data(
        $cartProvider,
        GenericEvent $event,
        OrderInterface $cart
    ) {
        $event->getSubject()->willReturn(null);

        $cartProvider->getCart()->willReturn($cart);

        $cart->getItems()->willReturn([]);

        $this->handleRestrictedZone($event);
    }

    function it_uses_cart_from_event(
        $cartProvider,
        GenericEvent $event,
        OrderInterface $cart
    ) {
        $event->getSubject()->willReturn($cart);

        $cartProvider->getCart()->shouldNotBeCalled();

        $cart->getItems()->willReturn([]);

        $this->handleRestrictedZone($event);
    }

    function it_validates_every_cart_item(
        $cartProvider,
        $restrictedZoneChecker,
        OrderItemInterface $item,
        GenericEvent $event,
        OrderInterface $cart,
        ProductInterface $product,
        AddressInterface $address
    ) {
        $event->getSubject()->willReturn($cart);

        $cartProvider->getCart()->shouldNotBeCalled();

        $item->getProduct()->willReturn($product);

        $cart->getItems()->willReturn([$item]);
        $cart->getShippingAddress()->willReturn($address);

        $restrictedZoneChecker->isRestricted($product, $address)->willReturn(false);

        $this->handleRestrictedZone($event);
    }

    function it_removes_invalid_cart_items(
        $cartProvider,
        $restrictedZoneChecker,
        $session,
        $translator,
        $cartManager,
        OrderItemInterface $item,
        GenericEvent $event,
        OrderInterface $cart,
        ProductInterface $product,
        AddressInterface $address,
        FlashBag $flashBag
    ) {
        $event->getSubject()->willReturn($cart);

        $cartProvider->getCart()->shouldNotBeCalled();

        $item->getProduct()->willReturn($product);

        $product->getName()->willReturn('invalid');

        $cart->getItems()->willReturn([$item]);
        $cart->getShippingAddress()->willReturn($address);
        $cart->removeItem($item)->shouldBeCalled();

        $restrictedZoneChecker->isRestricted($product, $address)->willReturn(true);

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('error', Argument::any())->shouldBeCalled();

        $translator->trans('sylius.cart.restricted_zone_removal', ['%product%' => 'invalid'], 'flashes')->shouldBeCalled();

        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();

        $this->handleRestrictedZone($event);
    }
}
