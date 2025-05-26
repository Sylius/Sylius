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

namespace Tests\Sylius\Bundle\AddressingBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\EventListener\ZoneMemberIntegrityListener;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ZoneMemberIntegrityListenerTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private MockObject&ZoneDeletionCheckerInterface $zoneDeletionChecker;

    private CountryProvincesDeletionCheckerInterface&MockObject $countryProvincesDeletionChecker;

    private MockObject&SessionInterface $session;

    private GenericEvent&MockObject $event;

    private ZoneMemberIntegrityListener $zoneMemberIntegrityListener;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->zoneDeletionChecker = $this->createMock(ZoneDeletionCheckerInterface::class);
        $this->countryProvincesDeletionChecker = $this->createMock(CountryProvincesDeletionCheckerInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->event = $this->createMock(GenericEvent::class);

        $this->zoneMemberIntegrityListener = new ZoneMemberIntegrityListener(
            $this->requestStack,
            $this->zoneDeletionChecker,
            $this->countryProvincesDeletionChecker,
        );
    }

    public function testDoesNotAllowToRemoveZoneIfItExistsAsAZoneMember(): void
    {
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);
        /** @var FlashBagInterface&MockObject $flashes */
        $flashes = $this->createMock(FlashBagInterface::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn($zone);
        $this->zoneDeletionChecker->expects($this->once())->method('isDeletable')->with($zone)->willReturn(false);
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);
        $this->session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashes);
        $flashes->expects($this->once())->method('add')->with('error', [
            'message' => 'sylius.resource.delete_error',
            'parameters' => ['%resource%' => 'Zone'],
        ])
        ;
        $this->event->expects($this->once())->method('stopPropagation');

        $this->zoneMemberIntegrityListener->protectFromRemovingZone($this->event);
    }

    public function testDoesNothingIfZoneDoesNotExistAsAZoneMember(): void
    {
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);

        $this->requestStack->expects($this->never())->method('getSession')->willReturn($this->session);
        $this->event->expects($this->once())->method('getSubject')->willReturn($zone);
        $this->zoneDeletionChecker->expects($this->once())->method('isDeletable')->with($zone)->willReturn(true);
        $this->session->expects($this->never())->method('getBag')->with('flashes');
        $this->event->expects($this->never())->method('stopPropagation');

        $this->zoneMemberIntegrityListener->protectFromRemovingZone($this->event);
    }

    public function testThrowsAnErrorIfAnEventSubjectIsNotAZone(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn('wrongSubject');

        $this->zoneMemberIntegrityListener->protectFromRemovingZone($this->event);
    }

    public function testDoesNotAllowToRemoveProvinceIfItExistsAsAZoneMember(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        /** @var FlashBagInterface&MockObject $flashes */
        $flashes = $this->createMock(FlashBagInterface::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn($country);
        $this->countryProvincesDeletionChecker->expects($this->once())->method('isDeletable')->with($country)->willReturn(false);
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);
        $this->session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashes);
        $flashes->expects($this->once())->method('add')->with('error', [
            'message' => 'sylius.resource.delete_error',
            'parameters' => ['%resource%' => 'Province'],
        ])
        ;
        $this->event->expects($this->once())->method('stopPropagation');

        $this->zoneMemberIntegrityListener->protectFromRemovingProvinceWithinCountry($this->event);
    }

    public function testDoesNothingIfProvinceDoesNotExistAsAZoneMember(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn($country);
        $this->countryProvincesDeletionChecker->expects($this->once())->method('isDeletable')->with($country)->willReturn(true);
        $this->session->expects($this->never())->method('getBag')->with('flashes');
        $this->event->expects($this->never())->method('stopPropagation');

        $this->zoneMemberIntegrityListener->protectFromRemovingProvinceWithinCountry($this->event);
    }

    public function testThrowsAnErrorIfAnEventSubjectIsNotAProvince(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn('wrongSubject');

        $this->zoneMemberIntegrityListener->protectFromRemovingProvinceWithinCountry($this->event);
    }

    public function testThrowsAnExceptionIfNoSessionIsAvailableDuringZoneProtection(): void
    {
        $this->expectException(SessionNotFoundException::class);

        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn($zone);
        $this->zoneDeletionChecker->expects($this->once())->method('isDeletable')->with($zone)->willReturn(false);
        $this->requestStack->expects($this->once())->method('getSession')->willThrowException(new SessionNotFoundException());

        $this->zoneMemberIntegrityListener->protectFromRemovingZone($this->event);
    }

    public function testThrowsAnExceptionIfNoSessionIsAvailableDuringProvinceProtection(): void
    {
        $this->expectException(SessionNotFoundException::class);

        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $this->event->expects($this->once())->method('getSubject')->willReturn($country);
        $this->countryProvincesDeletionChecker->expects($this->once())->method('isDeletable')->with($country)->willReturn(false);
        $this->requestStack->expects($this->once())->method('getSession')->willThrowException(new SessionNotFoundException());

        $this->zoneMemberIntegrityListener->protectFromRemovingProvinceWithinCountry($this->event);
    }
}
