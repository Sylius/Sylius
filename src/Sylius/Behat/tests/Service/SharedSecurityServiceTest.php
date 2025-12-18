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

namespace Tests\Sylius\Behat\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedSecurityService;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SharedSecurityServiceTest extends TestCase
{
    private MockObject&SecurityServiceInterface $adminSecurityService;

    private SharedSecurityService $sharedSecurityService;

    protected function setUp(): void
    {
        $this->adminSecurityService = $this->createMock(SecurityServiceInterface::class);

        $this->sharedSecurityService = new SharedSecurityService($this->adminSecurityService);
    }

    public function testImplementsSharedSecurityService(): void
    {
        $this->assertInstanceOf(SharedSecurityServiceInterface::class, $this->sharedSecurityService);
    }

    public function testPerformsActionAsGivenAdminUserAndRestorePreviousToken(): void
    {
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var AdminUserInterface&MockObject $adminUser */
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->adminSecurityService->expects($this->once())->method('getCurrentToken')->willReturn($token);
        $this->adminSecurityService->expects($this->once())->method('logIn')->with($adminUser);
        $order->expects($this->once())->method('completeCheckout');
        $this->adminSecurityService->expects($this->once())->method('restoreToken')->with($token);
        $this->adminSecurityService->expects($this->never())->method('logOut');

        $this->sharedSecurityService->performActionAsAdminUser(
            $adminUser,
            function () use ($order) {
                $order->completeCheckout();
            },
        );
    }

    public function testPerformsActionAsGivenAdminUserAndLogout(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var AdminUserInterface&MockObject $adminUser */
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->adminSecurityService->expects($this->once())->method('getCurrentToken')->willThrowException(new TokenNotFoundException());
        $this->adminSecurityService->expects($this->once())->method('logIn')->with($adminUser);
        $order->expects($this->once())->method('completeCheckout');
        $this->adminSecurityService->expects($this->never())->method('restoreToken');
        $this->adminSecurityService->expects($this->once())->method('logOut');

        $this->sharedSecurityService->performActionAsAdminUser(
            $adminUser,
            function () use ($order) {
                $order->completeCheckout();
            },
        );
    }
}
