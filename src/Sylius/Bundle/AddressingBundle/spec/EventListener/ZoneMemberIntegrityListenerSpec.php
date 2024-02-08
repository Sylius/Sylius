<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AddressingBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ZoneMemberIntegrityListenerSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
    ): void {
        $this->beConstructedWith($requestStack, $zoneDeletionChecker, $countryProvincesDeletionChecker);
    }

    function it_does_not_allow_to_remove_zone_if_it_exists_as_a_zone_member(
        RequestStack $requestStack,
        SessionInterface $session,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        GenericEvent $event,
        ZoneInterface $zone,
        FlashBagInterface $flashes,
        Request $request,
    ): void {
        $event->getSubject()->willReturn($zone);

        $zoneDeletionChecker->isDeletable($zone)->willReturn(false);

        $requestStack->getSession()->willReturn($session);

        $session->getBag('flashes')->willReturn($flashes);

        $flashes
            ->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'zone'],
            ])
            ->shouldBeCalled()
        ;

        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingZone($event);
    }

    function it_does_nothing_if_zone_does_not_exist_as_a_zone_member(
        RequestStack $requestStack,
        SessionInterface $session,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        GenericEvent $event,
        ZoneInterface $zone,
        Request $request,
    ): void {
        $requestStack->getSession()->willReturn($session);

        $event->getSubject()->willReturn($zone);

        $zoneDeletionChecker->isDeletable($zone)->willReturn(true);

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

    function it_does_not_allow_to_remove_province_if_it_exists_as_a_zone_member(
        RequestStack $requestStack,
        SessionInterface $session,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        GenericEvent $event,
        CountryInterface $country,
        FlashBagInterface $flashes,
        Request $request,
    ): void {
        $event->getSubject()->willReturn($country);

        $countryProvincesDeletionChecker->isDeletable($country)->willReturn(false);

        $requestStack->getSession()->willReturn($session);

        $session->getBag('flashes')->willReturn($flashes);

        $flashes
            ->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'province'],
            ])
            ->shouldBeCalled()
        ;

        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingProvinceWithinCountry($event);
    }

    function it_does_nothing_if_province_does_not_exist_as_a_zone_member(
        SessionInterface $session,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        GenericEvent $event,
        CountryInterface $country,
    ): void {
        $event->getSubject()->willReturn($country);

        $countryProvincesDeletionChecker->isDeletable($country)->willReturn(true);

        $session->getBag('flashes')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingProvinceWithinCountry($event);
    }

    function it_throws_an_error_if_an_event_subject_is_not_a_province(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingProvinceWithinCountry', [$event])
        ;
    }

    function it_throws_an_exception_if_no_session_is_available_during_zone_protection(
        ZoneInterface $zone,
        GenericEvent $event,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        RequestStack $requestStack,
    ): void {
        $event->getSubject()->willReturn($zone);

        $zoneDeletionChecker->isDeletable($zone)->willReturn(false);

        $requestStack->getSession()->willThrow(new SessionNotFoundException());

        $this
            ->shouldThrow(SessionNotFoundException::class)
            ->during('protectFromRemovingZone', [$event])
        ;
    }

    function it_throws_an_exception_if_no_session_is_available_during_province_protection(
        CountryInterface $country,
        GenericEvent $event,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        RequestStack $requestStack,
    ): void {
        $event->getSubject()->willReturn($country);

        $countryProvincesDeletionChecker->isDeletable($country)->willReturn(false);

        $requestStack->getSession()->willThrow(new SessionNotFoundException());

        $this
            ->shouldThrow(SessionNotFoundException::class)
            ->during('protectFromRemovingProvinceWithinCountry', [$event])
        ;
    }
}
