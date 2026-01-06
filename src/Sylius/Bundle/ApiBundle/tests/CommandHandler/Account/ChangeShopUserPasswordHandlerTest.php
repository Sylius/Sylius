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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ChangeShopUserPasswordHandlerTest extends TestCase
{
    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private MockObject&UserRepositoryInterface $userRepository;

    private ChangeShopUserPasswordHandler $handler;

    private MockObject&ShopUserInterface $shopUser;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->handler = new ChangeShopUserPasswordHandler($this->passwordUpdater, $this->userRepository);
        $this->shopUser = $this->createMock(ShopUserInterface::class);
    }

    public function testUpdatesUserPassword(): void
    {
        $this->userRepository->expects(self::once())->method('find')->with(42)->willReturn($this->shopUser);
        $this->shopUser->expects(self::once())->method('setPlainPassword')->with('PLAIN_PASSWORD');
        $this->passwordUpdater->expects(self::once())->method('updatePassword')->with($this->shopUser);
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        $this->handler->__invoke($changePasswordShopUser);
    }

    public function testThrowsExceptionIfNewPasswordsDoNotMatch(): void
    {
        $this->userRepository->expects(self::never())->method('find');
        $this->shopUser->expects(self::never())->method('setPlainPassword');
        $this->passwordUpdater->expects(self::never())->method('updatePassword');
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'WRONG_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($changePasswordShopUser);
    }

    public function testThrowsExceptionIfShopUserHasNotBeenFound(): void
    {
        $this->userRepository->expects(self::once())->method('find')->with(42)->willReturn(null);
        $this->shopUser->expects(self::never())->method('setPlainPassword');
        $this->passwordUpdater->expects(self::never())->method('updatePassword');
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($changePasswordShopUser);
    }
}
