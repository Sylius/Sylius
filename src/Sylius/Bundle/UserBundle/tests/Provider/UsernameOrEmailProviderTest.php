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

namespace Tests\Sylius\Bundle\UserBundle\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\RuntimeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UsernameOrEmailProviderTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private CanonicalizerInterface&MockObject $canonicalizer;

    private UsernameOrEmailProvider $usernameOrEmailProvider;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);

        $this->usernameOrEmailProvider = new UsernameOrEmailProvider(
            User::class,
            $this->userRepository,
            $this->canonicalizer,
        );
    }

    public function testImplementsSymfonyUserProviderInterface(): void
    {
        $this->assertInstanceOf(UserProviderInterface::class, $this->usernameOrEmailProvider);
    }

    public function testSupportsSyliusUserModel(): void
    {
        $this->assertTrue($this->usernameOrEmailProvider->supportsClass(User::class));
    }

    public function testDoesNotSupportOtherClasses(): void
    {
        $this->assertFalse($this->usernameOrEmailProvider->supportsClass('Sylius\Component\User\Model\CustomerGroupInterface'));
        $this->assertFalse($this->usernameOrEmailProvider->supportsClass('Acme\Fake\Class'));
    }

    public function testLoadsUserByUsername(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->canonicalizer
            ->expects($this->once())
            ->method('canonicalize')
            ->with('testUser')
            ->willReturn('testuser')
        ;
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['usernameCanonical' => 'testuser'])
            ->willReturn($user)
        ;

        $this->assertSame($user, $this->usernameOrEmailProvider->loadUserByUsername('testUser'));
    }

    public function testThrowsExceptionWhenThereIsNoUserWithGivenUsernameOrEmail(): void
    {
        $this->canonicalizer
            ->expects($this->once())
            ->method('canonicalize')
            ->with('testUser')
            ->willReturn('testuser')
        ;
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['usernameCanonical' => 'testuser'])
            ->willReturn(null)
        ;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Username "testuser" does not exist.');

        $this->usernameOrEmailProvider->loadUserByUsername('testUser');
    }

    public function testLoadsUserByEmail(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->canonicalizer
            ->expects($this->once())
            ->method('canonicalize')
            ->with('test@user.com')
            ->willReturn('test@user.com')
        ;
        $this->userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->with('test@user.com')
            ->willReturn($user)
        ;

        $this->assertSame($user, $this->usernameOrEmailProvider->loadUserByUsername('test@user.com'));
    }

    public function testRefreshesUser(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        /** @var UserInterface&MockObject $refreshedUser */
        $refreshedUser = $this->createMock(UserInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($refreshedUser)
        ;
        $user->expects($this->once())->method('getId')->willReturn(1);

        $this->assertSame($refreshedUser, $this->usernameOrEmailProvider->refreshUser($user));
    }

    public function testThrowExceptionWhenUnsupportedUserIsUsed(): void
    {
        /** @var \Symfony\Component\Security\Core\User\UserInterface&MockObject $user */
        $user = $this->createMock(\Symfony\Component\Security\Core\User\UserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->usernameOrEmailProvider->refreshUser($user);
    }
}
