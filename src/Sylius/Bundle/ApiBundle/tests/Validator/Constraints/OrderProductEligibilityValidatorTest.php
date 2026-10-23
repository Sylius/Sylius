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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class OrderProductEligibilityValidatorTest extends TestCase
{
    private MockObject|OrderRepositoryInterface $orderRepository;

    private ExecutionContextInterface|MockObject $context;

    private OrderProductEligibilityValidator $validator;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new OrderProductEligibilityValidator($this->orderRepository);
        $this->validator->initialize($this->context);
    }

    public function test_it_adds_violation_when_variant_is_disabled(): void
    {
        $orderToken = 'ORDER_TOKEN';
        $variantName = 'Variant 1';

        $command = new CompleteOrder($orderToken);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('isEnabled')->willReturn(false);
        $variant->method('getName')->willReturn($variantName);

        $product = $this->createMock(ProductInterface::class);
        $product->method('isEnabled')->willReturn(true);
        $product->method('hasChannel')->willReturn(true);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getProduct')->willReturn($product);

        $channel = $this->createMock(ChannelInterface::class);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $order->method('getChannel')->willReturn($channel);

        $this->orderRepository->method('findOneBy')->with(['tokenValue' => $orderToken])->willReturn($order);

        $constraint = new OrderProductEligibility();

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->context->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setParameter')->with('%productName%', $variantName)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setCode')->with(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($command, $constraint);
    }

    public function test_it_adds_violation_when_product_is_disabled(): void
    {
        $orderToken = 'ORDER_TOKEN';
        $productName = 'Product 2';

        $command = new CompleteOrder($orderToken);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('isEnabled')->willReturn(true);

        $product = $this->createMock(ProductInterface::class);
        $product->method('isEnabled')->willReturn(false);
        $product->method('getName')->willReturn($productName);
        $product->method('hasChannel')->willReturn(true);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getProduct')->willReturn($product);

        $channel = $this->createMock(ChannelInterface::class);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $order->method('getChannel')->willReturn($channel);

        $this->orderRepository->method('findOneBy')->with(['tokenValue' => $orderToken])->willReturn($order);

        $constraint = new OrderProductEligibility();

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->context->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setParameter')->with('%productName%', $productName)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setCode')->with(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($command, $constraint);
    }

    public function test_it_adds_violation_when_product_is_not_assigned_to_channel(): void
    {
        $orderToken = 'ORDER_TOKEN';
        $productName = 'Product 3';

        $command = new CompleteOrder($orderToken);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('isEnabled')->willReturn(true);

        $product = $this->createMock(ProductInterface::class);
        $product->method('isEnabled')->willReturn(true);
        $product->method('getName')->willReturn($productName);
        $product->method('hasChannel')->willReturn(false);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getProduct')->willReturn($product);

        $channel = $this->createMock(ChannelInterface::class);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $order->method('getChannel')->willReturn($channel);

        $this->orderRepository->method('findOneBy')->with(['tokenValue' => $orderToken])->willReturn($order);

        $constraint = new OrderProductEligibility();

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->context->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setParameter')->with('%productName%', $productName)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('setCode')->with(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($command, $constraint);
    }

    public function test_it_does_nothing_when_all_products_are_eligible(): void
    {
        $orderToken = 'ORDER_TOKEN';

        $command = new CompleteOrder($orderToken);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('isEnabled')->willReturn(true);

        $product = $this->createMock(ProductInterface::class);
        $product->method('isEnabled')->willReturn(true);
        $product->method('hasChannel')->willReturn(true);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getProduct')->willReturn($product);

        $channel = $this->createMock(ChannelInterface::class);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $order->method('getChannel')->willReturn($channel);

        $this->orderRepository->method('findOneBy')->with(['tokenValue' => $orderToken])->willReturn($order);

        $constraint = new OrderProductEligibility();

        $this->context
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($command, $constraint);
    }
}
