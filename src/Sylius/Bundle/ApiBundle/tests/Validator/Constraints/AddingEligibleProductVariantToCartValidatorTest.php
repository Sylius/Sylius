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

use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddingEligibleProductVariantToCartValidatorTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private AddingEligibleProductVariantToCartValidator $addingEligibleProductVariantToCartValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->addingEligibleProductVariantToCartValidator = new AddingEligibleProductVariantToCartValidator($this->productVariantRepository, $this->orderRepository, $this->availabilityChecker);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->addingEligibleProductVariantToCartValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfAddItemToCartCommand(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->addingEligibleProductVariantToCartValidator->validate(new CompleteOrder('TOKEN'), new AddingEligibleProductVariantToCart());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfAddingEligibleProductVariantToCart(): void
    {
        self::expectException(InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            $invalidConstraint,
        );
    }

    public function testDoesNothingIfOrderIsAlreadyPlaced(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(null);
        $this->productVariantRepository->expects(self::never())->method('findOneBy')->with(['code' => 'productVariantCode']);
        $executionContextMock->expects(self::never())->method('addViolation')->with($this->any());
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn(null);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductIsDisabled(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(false);
        $productMock->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantIsDisabled(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(false);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficientAndCartHasSameUnits(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Collection|MockObject $itemsMock */
        $itemsMock = $this->createMock(Collection::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $itemProductVariantMock */
        $itemProductVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::atLeastOnce())->method('getCode')->willReturn('productVariantCode');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getItems')->willReturn($itemsMock);
        $productVariantMock->expects(self::once())->method('isTracked')->willReturn(true);
        $itemsMock->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$orderItemMock]));
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($itemProductVariantMock);
        $orderItemMock->expects(self::once())->method('getQuantity')->willReturn(1);
        $itemProductVariantMock->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 2)->willReturn(false);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficientAndCartHasNotSameUnits(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Collection|MockObject $itemsMock */
        $itemsMock = $this->createMock(Collection::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $orderItemVariantMock */
        $orderItemVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $productVariantMock->method('isTracked')->willReturn(true);
        $cartMock->expects(self::once())->method('getItems')->willReturn($itemsMock);
        $orderItemMock->method('getVariant')->willReturn($orderItemVariantMock);
        $orderItemVariantMock->method('getCode')->willReturn('otherProductVariantCode');
        $itemsMock->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([]));
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 1)->willReturn(false);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductIsNotAvailableInChannel(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Collection|MockObject $itemsMock */
        $itemsMock = $this->createMock(Collection::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $itemProductVariantMock */
        $itemProductVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getItems')->willReturn($itemsMock);
        $productVariantMock->method('isTracked')->willReturn(true);
        $itemsMock->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$orderItemMock]));
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($itemProductVariantMock);
        $orderItemMock->method('getQuantity')->willReturn(1);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 1)->willReturn(true);
        $productMock->expects(self::once())->method('hasChannel')->with($channelMock)->willReturn(false);
        $productMock->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
        ;
        $this->addingEligibleProductVariantToCartValidator->validate($command, new AddingEligibleProductVariantToCart());
    }

    public function testDoesNothingIfProductAndVariantAreEnabledAndAvailableInChannel(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Collection|MockObject $itemsMock */
        $itemsMock = $this->createMock(Collection::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $itemProductVariantMock */
        $itemProductVariantMock = $this->createMock(ProductVariantInterface::class);
        $this->addingEligibleProductVariantToCartValidator->initialize($executionContextMock);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())->method('findOneBy')->with(['code' => 'productVariantCode'])->willReturn($productVariantMock);
        $productVariantMock->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $productVariantMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $productVariantMock->expects(self::once())->method('getProduct')->willReturn($productMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getItems')->willReturn($itemsMock);
        $productVariantMock->method('isTracked')->willReturn(true);
        $itemsMock->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$orderItemMock]));
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($itemProductVariantMock);
        $orderItemMock->method('getQuantity')->willReturn(1);
        $itemsMock->method('isEmpty')->willReturn(true);
        $productMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 1)->willReturn(true);
        $productMock->expects(self::once())->method('hasChannel')->with($channelMock)->willReturn(true);
        $productMock->method('getName')->willReturn('PRODUCT NAME');
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $executionContextMock->expects(self::never())->method('addViolation')->willReturnMap([['sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']], ['sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']], ['sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode']]]);
        $this->addingEligibleProductVariantToCartValidator->validate($command, new AddingEligibleProductVariantToCart());
    }
}
