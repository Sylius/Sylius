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

    private ExecutionContextInterface&MockObject $executionContext;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&ProductInterface $product;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemRepository = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->changedItemQuantityInCartValidator = new ChangedItemQuantityInCartValidator(
            $this->orderItemRepository,
            $this->orderRepository,
            $this->availabilityChecker,
        );
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->changedItemQuantityInCartValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfChangeItemQuantityInCart(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->changedItemQuantityInCartValidator->validate(
            new CompleteOrder('TOKEN'),
            new AddingEligibleProductVariantToCart(),
        );
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
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn(null);
        self::expectException(OrderItemNotFoundException::class);
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductVariantDoesNotExist(): void
    {
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn(null);
        $this->orderItem->expects(self::once())->method('getVariantName')->willReturn('MacPro');
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'MacPro']);
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductIsDisabled(): void
    {
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->method('getVariantName')->willReturn('Variant Name');
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $this->product->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->product->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']);
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductVariantIsDisabled(): void
    {
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects(self::once())->method('getVariantName')->willReturn('Variant Name');
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->product->method('getName')->willReturn('PRODUCT NAME');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'Variant Name']);
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficient(): void
    {
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->method('getVariantName')->willReturn('Variant Name');
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->product->method('getName')->willReturn('PRODUCT NAME');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 2)
            ->willReturn(false);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'VARIANT_CODE']);
        $this->changedItemQuantityInCartValidator->validate(
            new ChangeItemQuantityInCart(orderTokenValue: 'token', orderItemId: 11, quantity: 2),
            new ChangedItemQuantityInCart(),
        );
    }

    public function testAddsViolationIfProductIsNotAvailableInChannel(): void
    {
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->method('getVariantName')->willReturn('Variant Name');
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('VARIANT_CODE');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->product->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 2)
            ->willReturn(true);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getChannel')->willReturn($this->channel);
        $this->product->expects(self::once())
            ->method('hasChannel')
            ->with($this->channel)
            ->willReturn(false);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'], );
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
        $this->changedItemQuantityInCartValidator->initialize($this->executionContext);
        $this->orderItemRepository->method('findOneByIdAndCartTokenValue')
            ->with('11', 'token')
            ->willReturn($this->orderItem);
        $this->orderItem->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->method('getVariantName')->willReturn('Variant Name');
        $this->productVariant->method('getProduct')->willReturn($this->product);
        $this->productVariant->method('getCode')->willReturn('VARIANT_CODE');
        $this->product->method('isEnabled')->willReturn(true);
        $this->product->method('getName')->willReturnMap([['PRODUCT NAME'], ['PRODUCT NAME']]);
        $this->availabilityChecker->method('isStockSufficient')
            ->with($this->productVariant, 2)->willReturn(true);
        $this->product->method('getName')->willReturn('PRODUCT NAME');
        $this->orderRepository->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($this->cart);
        $this->cart->method('getChannel')->willReturn($this->channel);
        $this->product->method('hasChannel')->with($this->channel)->willReturn(true);
        $this->executionContext->expects(self::never())
            ->method('addViolation')
            ->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']);
        $this->executionContext->expects(self::never())
            ->method('addViolation')
            ->willReturnMap([
                ['sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']],
                ['sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']],
                ['sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode']],
            ]);
    }
}
