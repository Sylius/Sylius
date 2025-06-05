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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Exception\OrderItemNotFoundException;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChangedItemQuantityInCartValidatorTest extends TestCase
{
    private MockObject&OrderItemRepositoryInterface $orderItemRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private ChangedItemQuantityInCartValidator $changedItemQuantityInCartValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemRepository = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->changedItemQuantityInCartValidator = new ChangedItemQuantityInCartValidator($this->orderItemRepository, $this->orderRepository, $this->availabilityChecker);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->changedItemQuantityInCartValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfChangeItemQuantityInCart(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->changedItemQuantityInCartValidator->validate(new CompleteOrder('TOKEN'), new AddingEligibleProductVariantToCart());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfChangedItemQuantityInCart(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $command = new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2);

        $this->changedItemQuantityInCartValidator->validate($command, $invalidConstraint);
    }

    public function testThrowsAnExceptionIfOrderItemDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn(null);
        self::expectException(OrderItemNotFoundException::class);
        $this->changedItemQuantityInCartValidator->validate(new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2), new ChangedItemQuantityInCart());
    }

    public function testAddsViolationIfProductVariantDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn($orderItemMock);
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn(null);
        $orderItemMock->expects(self::once())->method('getVariantName')->willReturn('MacPro');
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'MacPro'])
        ;
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductIsDisabled(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn($orderItemMock);
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->method('getVariantName')->willReturn('Variant Name');
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $productMock->expects(self::once())->method('isEnabled')->willReturn(false);
        $productMock->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
        ;
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductVariantIsDisabled(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn($orderItemMock);
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->expects(self::once())->method('getVariantName')->willReturn('Variant Name');
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productMock->method('getName')->willReturn('PRODUCT NAME');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(false);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'Variant Name'])
        ;
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficient(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn($orderItemMock);
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->method('getVariantName')->willReturn('Variant Name');
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productMock->method('getName')->willReturn('PRODUCT NAME');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 2)->willReturn(false);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'VARIANT_CODE'])
        ;
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductIsNotAvailableInChannel(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);

        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);

        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($orderItemMock);

        $orderItemMock->expects(self::once())
            ->method('getVariant')
            ->willReturn($productVariantMock);
        $orderItemMock
            ->method('getVariantName')
            ->willReturn('Variant Name');

        $productVariantMock->expects(self::once())
            ->method('getProduct')
            ->willReturn($productMock);
        $productVariantMock->expects(self::once())
            ->method('getCode')
            ->willReturn('VARIANT_CODE');
        $productVariantMock->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $productMock->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $productMock->expects(self::once())
            ->method('getName')
            ->willReturn('PRODUCT NAME');

        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($productVariantMock, 2)
            ->willReturn(true);

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($cartMock);
        $cartMock->expects(self::once())
            ->method('getChannel')
            ->willReturn($channelMock);

        $productMock->expects(self::once())
            ->method('hasChannel')
            ->with($channelMock)
            ->willReturn(false);

        $executionContextMock->expects(self::once())
            ->method('addViolation')
            ->with(
                'sylius.product.not_exist',
                ['%productName%' => 'PRODUCT NAME'],
            );

        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(
                orderTokenValue: 'token',
                orderItemId: 11,
                quantity: 2,
            ),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testDoesNothingIfProductAndVariantAreEnabledAndAvailableInChannel(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        $this->changedItemQuantityInCartValidator->initialize($executionContextMock);
        $this->orderItemRepository->method('findOneByIdAndCartTokenValue')->with('11', 'token')->willReturn($orderItemMock);
        $orderItemMock->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->method('getVariantName')->willReturn('Variant Name');
        $productVariantMock->method('getProduct')->willReturn($productMock);
        $productVariantMock->method('getCode')->willReturn('VARIANT_CODE');
        $productMock->method('isEnabled')->willReturn(true);
        $productMock->method('getName')->willReturnMap([['PRODUCT NAME'], ['PRODUCT NAME']]);
        $this->availabilityChecker->method('isStockSufficient')->with($productVariantMock, 2)->willReturn(true);
        $productMock->method('getName')->willReturn('PRODUCT NAME');
        $this->orderRepository->method('findCartByTokenValue')->with('token')->willReturn($cartMock);
        $cartMock->method('getChannel')->willReturn($channelMock);
        $productMock->method('hasChannel')->with($channelMock)->willReturn(true);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
        ;
        $executionContextMock->expects(self::never())->method('addViolation')->willReturnMap([['sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']], ['sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']], ['sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode']]]);
    }
}
