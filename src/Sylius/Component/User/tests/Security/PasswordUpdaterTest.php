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

namespace Tests\Sylius\Component\User\Security;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdater;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordUpdaterTest extends TestCase
{
    /** @var UserPasswordHasherInterface&MockObject */
    private MockObject $userPasswordHasherMock;

    /** @var UserInterface&MockObject */
    private MockObject $userMock;

    private PasswordUpdater $passwordUpdater;

    protected function setUp(): void
    {
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->userMock = $this->createMock(UserInterface::class);
        $this->passwordUpdater = new PasswordUpdater($this->userPasswordHasherMock);
    }

    public function testShouldImplementPasswordUpdaterInterface(): void
    {
        $this->assertInstanceOf(PasswordUpdaterInterface::class, $this->passwordUpdater);
    }

    public function testShouldUpdateUserProfileWithHashedPassword(): void
    {
        $plainPassword = 'topSecretPlainPassword';
        $hashedPassword = 'topSecretHashedPassword';

        $this->userMock->expects($this->exactly(2))->method('getPlainPassword')->willReturn($plainPassword);
        $this->userPasswordHasherMock->expects($this->once())->method('hashPassword')->with($this->userMock, $plainPassword)->willReturn($hashedPassword);
        $this->userMock->expects($this->once())->method('eraseCredentials');
        $this->userMock->expects($this->once())->method('setPassword')->with($hashedPassword);

        $this->passwordUpdater->updatePassword($this->userMock);
    }

    public function testShouldDoNothingIfPlainPasswordIsEmpty(): void
    {
        $plainPassword = '';

        $this->userMock->expects($this->once())->method('getPlainPassword')->willReturn($plainPassword);
        $this->userMock->expects($this->never())->method('eraseCredentials');
        $this->userMock->expects($this->never())->method('setPassword');

        $this->passwordUpdater->updatePassword($this->userMock);
    }

    public function testShouldDoNothingIfPlainPasswordIsNull(): void
    {
        $plainPassword = null;

        $this->userMock->expects($this->once())->method('getPlainPassword')->willReturn($plainPassword);
        $this->userMock->expects($this->never())->method('eraseCredentials');
        $this->userMock->expects($this->never())->method('setPassword');

        $this->passwordUpdater->updatePassword($this->userMock);
    }
}
