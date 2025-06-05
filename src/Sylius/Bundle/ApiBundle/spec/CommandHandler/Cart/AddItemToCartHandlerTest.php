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

final class AddItemToCartHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var ProductVariantRepositoryInterface|MockObject */
    private MockObject $productVariantRepositoryMock;

    /** @var OrderModifierInterface|MockObject */
    private MockObject $orderModifierMock;

    /** @var CartItemFactoryInterface|MockObject */
    private MockObject $cartItemFactoryMock;

    /** @var OrderItemQuantityModifierInterface|MockObject */
    private MockObject $orderItemQuantityModifierMock;

    private AddItemToCartHandler $addItemToCartHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->productVariantRepositoryMock = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->orderModifierMock = $this->createMock(OrderModifierInterface::class);
        $this->cartItemFactoryMock = $this->createMock(CartItemFactoryInterface::class);
        $this->orderItemQuantityModifierMock = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->addItemToCartHandler = new AddItemToCartHandler($this->orderRepositoryMock, $this->productVariantRepositoryMock, $this->orderModifierMock, $this->cartItemFactoryMock, $this->orderItemQuantityModifierMock);
    }

    public function testAddsSimpleProductToCart(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $cartItemMock */
        $cartItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->orderRepositoryMock->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $this->productVariantRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($productVariantMock)
        ;
        $this->cartItemFactoryMock->expects(self::once())->method('createNew')->willReturn($cartItemMock);
        $cartItemMock->expects(self::once())->method('setVariant')->with($productVariantMock);
        $this->orderItemQuantityModifierMock->expects(self::once())->method('modify')->with($cartItemMock, 5);
        $this->orderModifierMock->expects(self::once())->method('addToOrder')->with($cartMock, $cartItemMock);
        self::assertSame($cartMock, $this(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 5,
        )));
    }

    public function testThrowsAnExceptionIfProductIsNotFound(): void
    {
        $this->productVariantRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'PRODUCT_VARIANT_CODE'])->willReturn(null);
        $this->cartItemFactoryMock->expects(self::never())->method('createNew');
        $this->expectException(InvalidArgumentException::class);
        $this->addItemToCartHandler->__invoke(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 1,
        ));
    }

    public function testThrowsAnExceptionIfCartIsNotFound(): void
    {
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($productVariantMock)
        ;
        $this->orderRepositoryMock->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(null);
        $this->cartItemFactoryMock->expects(self::never())->method('createNew');
        $this->expectException(InvalidArgumentException::class);
        $this->addItemToCartHandler->__invoke(new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'PRODUCT_VARIANT_CODE',
            quantity: 1,
        ));
    }
}
