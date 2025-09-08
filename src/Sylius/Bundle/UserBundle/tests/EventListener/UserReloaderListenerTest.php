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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\EventListener\UserReloaderListener;
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UserReloaderListenerTest extends TestCase
{
    private MockObject&UserReloaderInterface $userReloader;

    private UserReloaderListener $userReloaderListener;

    protected function setUp(): void
    {
        $this->userReloader = $this->createMock(UserReloaderInterface::class);

        $this->userReloaderListener = new UserReloaderListener($this->userReloader);
    }

    public function testReloadsUser(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($user);
        $this->userReloader->expects($this->once())->method('reloadUser')->with($user);

        $this->userReloaderListener->reloadUser($event);
    }

    public function testThrowsExceptionWhenReloadingNotAUserInterface(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('user');
        $this->userReloader->expects($this->never())->method('reloadUser');

        $this->expectException(\InvalidArgumentException::class);

        $this->userReloaderListener->reloadUser($event);
    }
}
