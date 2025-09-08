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
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\AddItemToCartHandler;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class AddItemToCartHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private MockObject&OrderModifierInterface $orderModifier;

    private CartItemFactoryInterface&MockObject $cartItemFactory;

    private MockObject&OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private AddItemToCartHandler $handler;

    private MockObject&ProductVariantInterface $productVariant;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);
        $this->cartItemFactory = $this->createMock(CartItemFactoryInterface::class);
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->handler = new AddItemToCartHandler(
            $this->orderRepository,
            $this->productVariantRepository,
            $this->orderModifier,
            $this->cartItemFactory,
            $this->orderItemQuantityModifier,
        );
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
    }

    public function testAddsSimpleProductToCart(): void
    {
        /** @var OrderInterface|MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $cartItem */
        $cartItem = $this->createMock(OrderItemInterface::class);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')->willReturn($cart);
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($this->productVariant);
        $this->cartItemFactory->expects(self::once())->method('createNew')->willReturn($cartItem);
        $cartItem->expects(self::once())->method('setVariant')->with($this->productVariant);
        $this->orderItemQuantityModifier->expects(self::once())->method('modify')->with($cartItem, 5);
        $this->orderModifier->expects(self::once())->method('addToOrder')->with($cart, $cartItem);
        self::assertSame($cart, $this->handler->__invoke(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 5,
        )));
    }

    public function testThrowsAnExceptionIfProductIsNotFound(): void
    {
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn(null);
        $this->cartItemFactory->expects(self::never())->method('createNew');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 1,
        ));
    }

    public function testThrowsAnExceptionIfCartIsNotFound(): void
    {
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($this->productVariant);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn(null);
        $this->cartItemFactory->expects(self::never())->method('createNew');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 1,
        ));
    }
}
