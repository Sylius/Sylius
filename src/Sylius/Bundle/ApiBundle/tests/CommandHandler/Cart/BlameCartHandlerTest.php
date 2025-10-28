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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class BlameCartHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $shopUserRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&OrderProcessorInterface $orderProcessor;

    private BlameCartHandler $handler;

    private OrderInterface $cart;

    private ShopUserInterface $user;

    private CustomerInterface $customerMock;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->handler = new BlameCartHandler($this->shopUserRepository, $this->orderRepository, $this->orderProcessor);
        $this->cart = $this->createMock(OrderInterface::class);
        $this->user = $this->createMock(ShopUserInterface::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);
    }

    public function testBlamesCartWithGivenData(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($this->user);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getCustomer')->willReturn(null);
        $this->user->expects(self::once())->method('getCustomer')->willReturn($this->customerMock);
        $this->cart->expects(self::once())->method('setCustomerWithAuthorization')->with($this->customerMock);
        $this->orderProcessor->expects(self::once())->method('process')->with($this->cart);
        $this->handler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfCartIsOccupied(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($this->user);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getCustomer')->willReturn($this->customerMock);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfCartHasNotBeenFound(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($this->user);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    public function testThrowsAnExceptionIfUserHasNotBeenFound(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new BlameCart('sylius@example.com', 'TOKEN'));
    }
}
