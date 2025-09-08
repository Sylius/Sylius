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

namespace Tests\Sylius\Bundle\AdminBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Context\AdminBasedLocaleContext;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AdminBasedLocaleContextTest extends TestCase
{
    private MockObject&TokenStorageInterface $tokenStorage;

    private AdminBasedLocaleContext $adminBasedLocaleContext;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->adminBasedLocaleContext = new AdminBasedLocaleContext($this->tokenStorage);
    }

    public function testImplementsLocaleContextInterface(): void
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->adminBasedLocaleContext);
    }

    public function testThrowsLocaleNotFoundExceptionWhenThereIsNoToken(): void
    {
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $this->expectException(LocaleNotFoundException::class);
        $this->adminBasedLocaleContext->getLocaleCode();
    }

    public function testThrowsLocaleNotFoundExceptionWhenThereIsNoUserInTheToken(): void
    {
        $token = $this->createMock(TokenInterface::class);

        $token->expects($this->once())->method('getUser')->willReturn(null);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $this->expectException(LocaleNotFoundException::class);
        $this->adminBasedLocaleContext->getLocaleCode();
    }

    public function testThrowsLocaleNotFoundExceptionWhenTheUserTakenFromTokenIsNotAnAdmin(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $user = $this->createMock(UserInterface::class);

        $token->expects($this->once())->method('getUser')->willReturn($user);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $this->expectException(LocaleNotFoundException::class);
        $this->adminBasedLocaleContext->getLocaleCode();
    }

    public function testReturnsLocaleOfCurrentlyLoggedAdminUser(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $admin = $this->createMock(AdminUserInterface::class);

        $admin->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $token->expects($this->once())->method('getUser')->willReturn($admin);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $this->assertSame('en_US', $this->adminBasedLocaleContext->getLocaleCode());
    }
}
