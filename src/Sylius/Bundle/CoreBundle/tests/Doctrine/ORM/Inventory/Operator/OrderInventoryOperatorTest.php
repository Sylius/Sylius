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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OrderInventoryOperatorTest extends TestCase
{
    private MockObject&OrderInventoryOperatorInterface $decoratedOperator;

    private EntityManagerInterface&MockObject $productVariantManager;

    private OrderInventoryOperator $orderInventoryOperator;

    protected function setUp(): void
    {
        $this->decoratedOperator = $this->createMock(OrderInventoryOperatorInterface::class);
        $this->productVariantManager = $this->createMock(EntityManagerInterface::class);
        $this->orderInventoryOperator = new OrderInventoryOperator(
            $this->decoratedOperator,
            $this->productVariantManager,
        );
    }

    public function testImplementsAnOrderInventoryOperatorInterface(): void
    {
        $this->assertInstanceOf(OrderInventoryOperatorInterface::class, $this->orderInventoryOperator);
    }

    public function testLocksTrackedVariantsDuringCancelling(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getVersion')->willReturn(7);

        $this->productVariantManager
            ->expects($this->once())
            ->method('lock')
            ->with($variant, LockMode::OPTIMISTIC, '7')
        ;

        $this->decoratedOperator->expects($this->once())->method('cancel')->with($order);

        $this->orderInventoryOperator->cancel($order);
    }

    public function testLocksTrackedVariantsDuringHolding(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getVersion')->willReturn(7);

        $this->productVariantManager
            ->expects($this->once())
            ->method('lock')
            ->with($variant, LockMode::OPTIMISTIC, '7')
        ;

        $this->decoratedOperator->expects($this->once())->method('hold')->with($order);

        $this->orderInventoryOperator->hold($order);
    }

    public function testLocksTrackedVariantsDuringSelling(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getVersion')->willReturn(7);

        $this->productVariantManager
            ->expects($this->once())
            ->method('lock')
            ->with($variant, LockMode::OPTIMISTIC, '7')
        ;

        $this->decoratedOperator->expects($this->once())->method('sell')->with($order);

        $this->orderInventoryOperator->sell($order);
    }
}
