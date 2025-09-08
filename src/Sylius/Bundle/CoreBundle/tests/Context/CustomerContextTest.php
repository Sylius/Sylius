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

namespace Tests\Sylius\Bundle\CoreBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Context\CustomerContext;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CustomerContextTest extends TestCase
{
    private MockObject&TokenStorageInterface $tokenStorage;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private CustomerContext $customerContext;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->customerContext = new CustomerContext(
            $this->tokenStorage,
            $this->authorizationChecker,
        );
    }

    public function testItGetsCustomerFromCurrentlyLoggedUser(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $this->tokenStorage->method('getToken')->willReturn($token);

        $this->authorizationChecker
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->willReturn(true)
        ;

        $token->method('getUser')->willReturn($user);
        $user->method('getCustomer')->willReturn($customer);

        $this->assertSame($customer, $this->customerContext->getCustomer());
    }

    public function testItReturnsNullIfUserIsNotLoggedIn(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $this->assertNull($this->customerContext->getCustomer());
    }

    public function testItReturnsNullIfUserIsNotAShopUserInstance(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $user = $this->createMock(UserInterface::class);

        $this->tokenStorage->method('getToken')->willReturn($token);

        $this->authorizationChecker
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->willReturn(true)
        ;

        $token->method('getUser')->willReturn($user);

        $this->assertNull($this->customerContext->getCustomer());
    }
}
