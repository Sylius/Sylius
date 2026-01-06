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

namespace Tests\Sylius\Bundle\AddressingBundle\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Form\EventListener\BuildZoneFormSubscriber;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class BuildZoneFormSubscriberTest extends TestCase
{
    private BuildZoneFormSubscriber $buildZoneFormSubscriber;

    protected function setUp(): void
    {
        $this->buildZoneFormSubscriber = new BuildZoneFormSubscriber();
    }

    public function testASubscriber(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->buildZoneFormSubscriber);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertSame([
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ], BuildZoneFormSubscriber::getSubscribedEvents());
    }

    public function testFixesMembersKeysOnPreSubmit(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);
        /** @var ZoneMemberInterface&MockObject $belgiumZone */
        $belgiumZone = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneMemberInterface&MockObject $germanyZone */
        $germanyZone = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneMemberInterface&MockObject $franceZone */
        $franceZone = $this->createMock(ZoneMemberInterface::class);

        $event->expects($this->once())->method('getData')->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => ['code' => 'EU-PL'],
                4 => ['code' => 'EU-FR'],
            ],
        ]);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn($zone);
        $belgiumZone->expects($this->once())->method('getCode')->willReturn('EU-BE');
        $germanyZone->expects($this->once())->method('getCode')->willReturn('EU-DE');
        $franceZone->expects($this->once())->method('getCode')->willReturn('EU-FR');
        $zone->expects($this->once())->method('getMembers')->willReturn(new ArrayCollection([
            $belgiumZone,
            $germanyZone,
            $franceZone,
        ]));
        $event->expects($this->once())->method('setData')->with([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                2 => ['code' => 'EU-FR'],
                3 => ['code' => 'EU-PL'],
            ],
        ]);

        $this->buildZoneFormSubscriber->preSubmit($event);
    }

    public function testDoesNothingIfThereAreNoMembersInTheFormData(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        $event->expects($this->once())->method('getData')->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
        ]);
        $event->expects($this->never())->method('setData');

        $this->buildZoneFormSubscriber->preSubmit($event);
    }

    public function testIgnoresMembersWithNoCode(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);
        /** @var ZoneMemberInterface&MockObject $belgiumZone */
        $belgiumZone = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneMemberInterface&MockObject $germanyZone */
        $germanyZone = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneMemberInterface&MockObject $franceZone */
        $franceZone = $this->createMock(ZoneMemberInterface::class);

        $event->expects($this->once())->method('getData')->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => [],
                4 => ['code' => 'EU-FR'],
            ],
        ]);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn($zone);
        $belgiumZone->expects($this->once())->method('getCode')->willReturn('EU-BE');
        $germanyZone->expects($this->once())->method('getCode')->willReturn('EU-DE');
        $franceZone->expects($this->once())->method('getCode')->willReturn('EU-FR');
        $zone->expects($this->once())->method('getMembers')->willReturn(new ArrayCollection([
            $belgiumZone,
            $germanyZone,
            $franceZone,
        ]));
        $event->expects($this->once())->method('setData')->with([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                2 => ['code' => 'EU-FR'],
            ],
        ]);

        $this->buildZoneFormSubscriber->preSubmit($event);
    }

    public function testThrowsAnExceptionIfFromDataIsNotAZone(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);

        $event->expects($this->once())->method('getData')->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => ['code' => 'EU-PL'],
                4 => ['code' => 'EU-FR'],
            ],
        ]);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn(null);

        $this->buildZoneFormSubscriber->preSubmit($event);
    }
}
