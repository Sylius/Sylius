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
use Sylius\Bundle\UserBundle\Provider\EmailProvider;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class EmailProviderTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private CanonicalizerInterface&MockObject $canonicalizer;

    private EmailProvider $emailProvider;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);

        $this->emailProvider = new EmailProvider(User::class, $this->userRepository, $this->canonicalizer);
    }

    public function testImplementsSymfonyUserProviderInterface(): void
    {
        $this->assertInstanceOf(UserProviderInterface::class, $this->emailProvider);
    }

    public function testSupportsSyliusUserModel(): void
    {
        $this->assertTrue($this->emailProvider->supportsClass(User::class));
    }

    public function testLoadsUserByEmail(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);

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

        $this->assertSame($user, $this->emailProvider->loadUserByUsername('test@user.com'));
    }

    public function testUpdatesUserByUserName(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);

        $this->userRepository->expects($this->once())->method('find')->with(1)->willReturn($user);
        $user->expects($this->once())->method('getId')->willReturn(1);

        $this->assertSame($user, $this->emailProvider->refreshUser($user));
    }

    public function testThrowExceptionWhenUnsupportedUserIsUsed(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->emailProvider->refreshUser($user);
    }
}
