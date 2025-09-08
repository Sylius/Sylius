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
use Sylius\Bundle\UserBundle\Provider\UsernameProvider;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UsernameProviderTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private CanonicalizerInterface&MockObject $canonicalizer;

    private UsernameProvider $usernameProvider;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);

        $this->usernameProvider = new UsernameProvider(
            User::class,
            $this->userRepository,
            $this->canonicalizer,
        );
    }

    public function testImplementsSymfonyUserProviderInterface(): void
    {
        $this->assertInstanceOf(UserProviderInterface::class, $this->usernameProvider);
    }

    public function testSupportsSyliusUserModel(): void
    {
        $this->assertTrue($this->usernameProvider->supportsClass(User::class));
    }

    public function testLoadsUserByUserName(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);

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

        $this->assertSame($user, $this->usernameProvider->loadUserByUsername('testUser'));
    }

    public function testUpdatesUserByUserName(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);

        $this->userRepository->expects($this->once())->method('find')->with(1)->willReturn($user);
        $user->expects($this->once())->method('getId')->willReturn(1);

        $this->assertSame($user, $this->usernameProvider->refreshUser($user));
    }

    public function testThrowExceptionWhenUnsupportedUserIsUsed(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->usernameProvider->refreshUser($user);
    }
}
