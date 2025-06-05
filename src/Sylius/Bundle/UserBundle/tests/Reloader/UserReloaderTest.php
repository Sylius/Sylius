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

namespace Tests\Sylius\Bundle\UserBundle\Reloader;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Reloader\UserReloader;
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\User\Model\UserInterface;

final class UserReloaderTest extends TestCase
{
    private MockObject&ObjectManager $objectManager;

    private UserReloader $userReloader;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);

        $this->userReloader = new UserReloader($this->objectManager);
    }

    public function testImplementsUserReloaderInterface(): void
    {
        $this->assertInstanceOf(UserReloaderInterface::class, $this->userReloader);
    }

    public function testReloadsUser(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->objectManager->expects($this->once())->method('refresh')->with($user);

        $this->userReloader->reloadUser($user);
    }
}
