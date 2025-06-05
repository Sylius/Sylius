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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Customer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Customer\RemoveShopUser;
use Sylius\Bundle\ApiBundle\CommandHandler\Customer\RemoveShopUserHandler;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class RemoveShopUserHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $userRepositoryMock;

    private RemoveShopUserHandler $removeShopUserHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->removeShopUserHandler = new RemoveShopUserHandler($this->userRepositoryMock);
    }

    public function testThrowsAnExceptionIfUserHasNotBeenFound(): void
    {
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        $this->removeShopUserHandler->__invoke(new RemoveShopUser(42));
    }

    public function testRemoveShopUser(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('setCustomer')->with(null);
        $this->userRepositoryMock->expects(self::once())->method('remove')->with($shopUserMock);
        $this->removeShopUserHandler->__invoke(new RemoveShopUser(42));
    }
}
