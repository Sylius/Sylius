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

namespace Tests\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PasswordUpdaterListenerTest extends TestCase
{
    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private PasswordUpdaterListener $passwordUpdaterListener;

    protected function setUp(): void
    {
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);

        $this->passwordUpdaterListener = new PasswordUpdaterListener($this->passwordUpdater);
    }

    public function testUpdatesPasswordForGenericEvent(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($user);
        $user->expects($this->once())->method('getPlainPassword')->willReturn('testPassword');
        $this->passwordUpdater->expects($this->once())->method('updatePassword')->with($user);

        $this->passwordUpdaterListener->genericEventUpdater($event);
    }

    public function testAllowsToUpdatePasswordForGenericEventForUserInterfaceImplementationOnly(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('user');

        $this->expectException(\TypeError::class);

        $this->passwordUpdaterListener->genericEventUpdater($event);
    }

    public function testUpdatesPasswordOnPrePersistDoctrineEvent(): void
    {
        /** @var LifecycleEventArgs&MockObject $event */
        $event = $this->createMock(LifecycleEventArgs::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getObject')->willReturn($user);
        $user->expects($this->once())->method('getPlainPassword')->willReturn('testPassword');
        $this->passwordUpdater->expects($this->once())->method('updatePassword')->with($user);

        $this->passwordUpdaterListener->prePersist($event);
    }

    public function testUpdatesPasswordOnPreUpdateDoctrineEvent(): void
    {
        /** @var LifecycleEventArgs&MockObject $event */
        $event = $this->createMock(LifecycleEventArgs::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getObject')->willReturn($user);
        $user->expects($this->once())->method('getPlainPassword')->willReturn('testPassword');
        $this->passwordUpdater->expects($this->once())->method('updatePassword')->with($user);

        $this->passwordUpdaterListener->preUpdate($event);
    }

    public function testUpdatesPasswordOnPrePersistDoctrineEventForUserInterfaceImplementationOnly(): void
    {
        /** @var LifecycleEventArgs&MockObject $event */
        $event = $this->createMock(LifecycleEventArgs::class);

        $event->expects($this->once())->method('getObject')->willReturn('user');
        $this->passwordUpdater->expects($this->never())->method('updatePassword');

        $this->passwordUpdaterListener->prePersist($event);
    }

    public function testUpdatesPasswordOnPreUpdateDoctrineEventForUserInterfaceImplementationOnly(): void
    {
        /** @var LifecycleEventArgs&MockObject $event */
        $event = $this->createMock(LifecycleEventArgs::class);

        $event->expects($this->once())->method('getObject')->willReturn('user');
        $this->passwordUpdater->expects($this->never())->method('updatePassword');

        $this->passwordUpdaterListener->preUpdate($event);
    }
}
