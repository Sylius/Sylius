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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class BlameCartHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $shopUserRepositoryMock;

    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var OrderProcessorInterface|MockObject */
    private MockObject $orderProcessorMock;

    private BlameCartHandler $blameCartHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->shopUserRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderProcessorMock = $this->createMock(OrderProcessorInterface::class);
        $this->blameCartHandler = new BlameCartHandler($this->shopUserRepositoryMock, $this->orderRepositoryMock, $this->orderProcessorMock);
    }

    public function testBlamesCartWithGivenData(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('sylius@example.com')->willReturn($userMock);
        $this->orderRepositoryMock->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getCustomer')->willReturn(null);
        $userMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $cartMock->expects(self::once())->method('setCustomerWithAuthorization')->with($customerMock);
        $this->orderProcessorMock->expects(self::once())->method('process')->with($cartMock);
        $this(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfCartIsOccupied(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('sylius@example.com')->willReturn($userMock);
        $this->orderRepositoryMock->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $this->expectException(InvalidArgumentException::class);
        $this->blameCartHandler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfCartHasNotBeenFound(): void
    {
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('sylius@example.com')->willReturn($userMock);
        $this->expectException(InvalidArgumentException::class);
        $this->blameCartHandler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfUserHasNotBeenFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->blameCartHandler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }
}
