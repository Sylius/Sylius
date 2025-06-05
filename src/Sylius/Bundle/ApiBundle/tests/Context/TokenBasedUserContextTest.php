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

namespace Tests\Sylius\Bundle\ApiBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\TokenBasedUserContext;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenBasedUserContextTest extends TestCase
{
    private MockObject&TokenStorageInterface $tokenStorage;

    private TokenBasedUserContext $tokenBasedUserContext;

    private MockObject&TokenInterface $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenBasedUserContext = new TokenBasedUserContext($this->tokenStorage);
        $this->token = $this->createMock(TokenInterface::class);
    }

    public function testImplementsUserContextInterface(): void
    {
        self::assertInstanceOf(UserContextInterface::class, $this->tokenBasedUserContext);
    }

    public function testReturnsUserFromToken(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->tokenStorage->expects(self::once())->method('getToken')->willReturn($this->token);

        $this->token->expects(self::once())->method('getUser')->willReturn($user);

        self::assertSame($user, $this->tokenBasedUserContext->getUser());
    }

    public function testReturnsNullIfUserFromTokenIsAnonymous(): void
    {
        $this->tokenStorage->expects(self::once())->method('getToken')->willReturn($this->token);

        $this->token->expects(self::once())->method('getUser')->willReturn(null);

        self::assertNull($this->tokenBasedUserContext->getUser());
    }

    public function testReturnsNullIfUserFromTokenIsNull(): void
    {
        $this->tokenStorage->expects(self::once())->method('getToken')->willReturn($this->token);

        $this->token->expects(self::once())->method('getUser')->willReturn(null);

        self::assertNull($this->tokenBasedUserContext->getUser());
    }

    public function testReturnsNullIfNoTokenIsSetInTokenStorage(): void
    {
        $this->tokenStorage->expects(self::once())->method('getToken')->willReturn(null);

        self::assertNull($this->tokenBasedUserContext->getUser());
    }
}
