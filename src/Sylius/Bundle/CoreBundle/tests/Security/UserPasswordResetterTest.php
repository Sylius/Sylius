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

namespace Tests\Sylius\Bundle\CoreBundle\Security;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetter;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Sylius\Bundle\UserBundle\Exception\UserNotFoundException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class UserPasswordResetterTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private UserPasswordResetter $userPasswordResetter;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $this->userPasswordResetter = new UserPasswordResetter(
            $this->userRepository,
            $this->passwordUpdater,
            'P5D',
        );
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(UserPasswordResetterInterface::class, $this->userPasswordResetter);
    }

    public function testResetsPassword(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->userRepository
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'TOKEN'])
            ->willReturn($user)
        ;

        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->callback(fn (\DateInterval $interval) => $interval->format('%d') === '5'))
            ->willReturn(true)
        ;

        $user->expects($this->once())->method('setPlainPassword')->with('newPassword');
        $this->passwordUpdater->expects($this->once())->method('updatePassword')->with($user);
        $user->expects($this->once())->method('setPasswordResetToken')->with(null);
        $user->expects($this->once())->method('setPasswordRequestedAt')->with(null);

        $this->userPasswordResetter->reset('TOKEN', 'newPassword');
    }

    public function testThrowsExceptionIfUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'TOKEN'])
            ->willReturn(null)
        ;

        $this->userPasswordResetter->reset('TOKEN', 'newPassword');
    }

    public function testThrowsExceptionIfTokenIsExpired(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->userRepository->method('findOneBy')->with(['passwordResetToken' => 'TOKEN'])->willReturn($user);

        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->callback(fn (\DateInterval $interval) => $interval->format('%d') === '5'))
            ->willReturn(false)
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->userPasswordResetter->reset('TOKEN', 'newPassword');
    }
}
