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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class ChangeShopUserPasswordHandlerTest extends TestCase
{
    /** @var PasswordUpdaterInterface|MockObject */
    private MockObject $passwordUpdaterMock;

    /** @var UserRepositoryInterface|MockObject */
    private MockObject $userRepositoryMock;

    private ChangeShopUserPasswordHandler $changeShopUserPasswordHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->passwordUpdaterMock = $this->createMock(PasswordUpdaterInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->changeShopUserPasswordHandler = new ChangeShopUserPasswordHandler($this->passwordUpdaterMock, $this->userRepositoryMock);
    }

    public function testUpdatesUserPassword(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('setPlainPassword')->with('PLAIN_PASSWORD');
        $this->passwordUpdaterMock->expects(self::once())->method('updatePassword')->with($shopUserMock);
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        $this($changePasswordShopUser);
    }

    public function testThrowsExceptionIfNewPasswordsDoNotMatch(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userRepositoryMock->expects(self::never())->method('find');
        $shopUserMock->expects(self::never())->method('setPlainPassword');
        $this->passwordUpdaterMock->expects(self::never())->method('updatePassword');
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'WRONG_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        $this->expectException(InvalidArgumentException::class);
        $this->changeShopUserPasswordHandler->__invoke($changePasswordShopUser);
    }

    public function testThrowsExceptionIfShopUserHasNotBeenFound(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn(null);
        $shopUserMock->expects(self::never())->method('setPlainPassword');
        $this->passwordUpdaterMock->expects(self::never())->method('updatePassword');
        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );
        $this->expectException(InvalidArgumentException::class);
        $this->changeShopUserPasswordHandler->__invoke($changePasswordShopUser);
    }
}
