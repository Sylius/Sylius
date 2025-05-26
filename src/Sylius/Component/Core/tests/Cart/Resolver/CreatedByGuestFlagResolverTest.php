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

namespace Tests\Sylius\Component\Core\Cart\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolver;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreatedByGuestFlagResolverTest extends TestCase
{
    private MockObject&TokenStorageInterface $tokenStorage;

    private MockObject&TokenInterface $token;

    private CreatedByGuestFlagResolver $resolver;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->resolver = new CreatedByGuestFlagResolver($this->tokenStorage);
    }

    public function testShouldImplementCreateByGuestFlagResolverInterface(): void
    {
        $this->assertInstanceOf(CreatedByGuestFlagResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnFalseIfThereIsLoggedInCustomer(): void
    {
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->createMock(UserInterface::class));

        $this->assertFalse($this->resolver->resolveFlag());
    }

    public function testShouldReturnTrueIfOrderIsCreatedByAnonymousUser(): void
    {
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->token->expects($this->once())->method('getUser')->willReturn(null);

        $this->assertTrue($this->resolver->resolveFlag());
    }

    public function testShouldReturnTrueIfThereIsNoToken(): void
    {
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);

        $this->assertTrue($this->resolver->resolveFlag());
    }
}
