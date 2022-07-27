<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AttributeBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Checker\AttributeDeletionCheckerInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class AttributeIntegrityListenerSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        AttributeDeletionCheckerInterface $attributeDeletionChecker,
    ): void {
        $this->beConstructedWith($requestStack, $attributeDeletionChecker);
    }

    function it_does_not_allow_to_remove_attribute_if_it_exists_as_a_product_attribute_member(
        RequestStack $requestStack,
        SessionInterface $session,
        AttributeDeletionCheckerInterface $attributeDeletionChecker,
        GenericEvent $event,
        ProductAttributeInterface $productAttribute,
        FlashBagInterface $flashes,
        Request $request,
    ): void {
        $event->getSubject()->willReturn($productAttribute);

        $attributeDeletionChecker->isDeletable($productAttribute)->willReturn(false);

        if (!method_exists(RequestStack::class, 'getSession')) {
            $requestStack->getMasterRequest()->willReturn($request);
            $request->getSession()->willReturn($session);
        } else {
            $requestStack->getSession()->willReturn($session);
        }

        $session->getBag('flashes')->willReturn($flashes);

        $flashes
            ->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'attribute'],
            ])
            ->shouldBeCalled()
        ;

        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingZone($event);
    }

    function it_does_nothing_if_attribute_does_not_exist_as_a_product_attribute_member(
        RequestStack $requestStack,
        SessionInterface $session,
        AttributeDeletionCheckerInterface $attributeDeletionChecker,
        GenericEvent $event,
        ProductAttributeInterface $productAttribute,
        Request $request,
    ): void {
        if (!method_exists(RequestStack::class, 'getSession')) {
            $requestStack->getMasterRequest()->willReturn($request);
            $request->getSession()->willReturn($session);
        } else {
            $requestStack->getSession()->willReturn($session);
        }

        $event->getSubject()->willReturn($productAttribute);

        $attributeDeletionChecker->isDeletable($productAttribute)->willReturn(true);

        $session->getBag('flashes')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingZone($event);
    }

    function it_throws_an_error_if_an_event_subject_is_not_a_zone(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingZone', [$event])
        ;
    }

    function it_throws_an_error_if_an_event_subject_is_not_a_province(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingProvinceWithinCountry', [$event])
        ;
    }
}
