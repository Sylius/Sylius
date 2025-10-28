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

    private ExecutionContextInterface&MockObject $executionContext;

    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&ProductInterface $product;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderItemInterface $orderItem;

    private Collection&MockObject $items;

    private MockObject&ProductVariantInterface $itemProductVariant;

    private MockObject&ProductVariantInterface $orderItemVariant;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->addingEligibleProductVariantToCartValidator = new AddingEligibleProductVariantToCartValidator(
            $this->productVariantRepository,
            $this->orderRepository,
            $this->availabilityChecker,
        );

        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->items = $this->createMock(Collection::class);
        $this->itemProductVariant = $this->createMock(ProductVariantInterface::class);
        $this->orderItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->addingEligibleProductVariantToCartValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfAddItemToCartCommand(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->addingEligibleProductVariantToCartValidator->validate(
            new CompleteOrder('TOKEN'),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfAddingEligibleProductVariantToCart(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $invalidConstraint = $this->createMock(Constraint::class);
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            $invalidConstraint,
        );
    }

    public function testDoesNothingIfOrderIsAlreadyPlaced(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')->willReturn(null);
        $this->productVariantRepository->expects(self::never())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode']);
        $this->executionContext->expects(self::never())->method('addViolation')->with($this->anything());
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantDoesNotExist(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')->with('TOKEN')
            ->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn(null);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']);
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductIsDisabled(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->product->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']);
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantIsDisabled(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn(new Order());
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode']);
        $this->addingEligibleProductVariantToCartValidator->validate(
            new AddItemToCart(orderTokenValue: 'TOKEN', productVariantCode: 'productVariantCode', quantity: 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficientAndCartHasSameUnits(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::atLeastOnce())->method('getCode')->willReturn('productVariantCode');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getItems')->willReturn($this->items);
        $this->productVariant->expects(self::once())->method('isTracked')->willReturn(true);
        $this->items->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$this->orderItem]));
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->itemProductVariant);
        $this->orderItem->expects(self::once())->method('getQuantity')->willReturn(1);
        $this->itemProductVariant->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 2)
            ->willReturn(false);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode']);
        $this->addingEligibleProductVariantToCartValidator->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductVariantStockIsNotSufficientAndCartHasNotSameUnits(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn($this->cart);
        $this->productVariant->method('isTracked')->willReturn(true);
        $this->cart->expects(self::once())->method('getItems')->willReturn($this->items);
        $this->orderItem->method('getVariant')->willReturn($this->orderItemVariant);
        $this->orderItemVariant->method('getCode')->willReturn('otherProductVariantCode');
        $this->items->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([]));
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 1)
            ->willReturn(false);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode']);
        $this->addingEligibleProductVariantToCartValidator->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    public function testAddsViolationIfProductIsNotAvailableInChannel(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn($this->cart);
        $this->cart->expects(self::once())
            ->method('getItems')
            ->willReturn($this->items);
        $this->productVariant->method('isTracked')->willReturn(true);
        $this->items->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$this->orderItem]));
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->itemProductVariant);
        $this->orderItem->method('getQuantity')->willReturn(1);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 1)
            ->willReturn(true);
        $this->product->expects(self::once())->method('hasChannel')->with($this->channel)->willReturn(false);
        $this->product->expects(self::once())->method('getName')->willReturn('PRODUCT NAME');
        $this->cart->expects(self::once())->method('getChannel')->willReturn($this->channel);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME']);
        $this->addingEligibleProductVariantToCartValidator->validate($command, new AddingEligibleProductVariantToCart());
    }

    public function testDoesNothingIfProductAndVariantAreEnabledAndAvailableInChannel(): void
    {
        $this->addingEligibleProductVariantToCartValidator->initialize($this->executionContext);
        $command = new AddItemToCart(
            orderTokenValue: 'TOKEN',
            productVariantCode: 'productVariantCode',
            quantity: 1,
        );
        $this->productVariantRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'productVariantCode'])
            ->willReturn($this->productVariant);
        $this->productVariant->expects(self::once())->method('getCode')->willReturn('productVariantCode');
        $this->productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->productVariant->expects(self::once())->method('getProduct')->willReturn($this->product);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getItems')->willReturn($this->items);
        $this->productVariant->method('isTracked')->willReturn(true);
        $this->items->expects(self::once())->method('getIterator')->willReturn(new \ArrayIterator([$this->orderItem]));
        $this->orderItem->expects(self::once())->method('getVariant')->willReturn($this->itemProductVariant);
        $this->orderItem->method('getQuantity')->willReturn(1);
        $this->items->method('isEmpty')->willReturn(true);
        $this->product->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($this->productVariant, 1)
            ->willReturn(true);
        $this->product->expects(self::once())->method('hasChannel')->with($this->channel)->willReturn(true);
        $this->product->method('getName')->willReturn('PRODUCT NAME');
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN')->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getChannel')->willReturn($this->channel);
        $this->executionContext->expects(self::never())
            ->method('addViolation')
            ->willReturnMap([
                [
                    'sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'],
                ],
                [
                    'sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'],
                ],
                [
                    'sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'],
                ],
            ]);
        $this->addingEligibleProductVariantToCartValidator->validate($command, new AddingEligibleProductVariantToCart());
    }
}
