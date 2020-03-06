<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Tests\Calculator;

use Mockery;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculator;
use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderItemsSubtotalCalculatorTest extends TestCase
{
    private function getOrderItemMock(int $subTotal): OrderItemInterface
    {
        $orderItem = Mockery::mock(OrderItemInterface::class);
        $orderItem
            ->shouldReceive('getSubTotal')
            ->once()
            ->andReturn($subTotal)
        ;

        return $orderItem;
    }

    private function getOrderMock(array $orderItems): OrderInterface
    {
        $order = Mockery::mock(OrderInterface::class);
        $order
            ->shouldReceive('getItems->toArray')
            ->once()
            ->andReturn($orderItems);

        return $order;
    }

    /**
     * @test
     */
    public function it_can_be_instantiated(): OrderItemsSubtotalCalculator
    {
        $calculator = new OrderItemsSubtotalCalculator();
        $this->assertInstanceOf(OrderItemsSubtotalCalculatorInterface::class, $calculator);

        return $calculator;
    }

    /**
     * @test
     * @depends it_can_be_instantiated
     * @param OrderItemsSubtotalCalculator $calculator
     */
    public function it_can_calculate_the_subtotal_of_order_items(OrderItemsSubtotalCalculator $calculator): void
    {
        $subTotal = $calculator->getSubtotal($this->getOrderMock([
            $this->getOrderItemMock(1000),
            $this->getOrderItemMock(1000)
        ]));
        $this->assertEquals(2000, $subTotal);
    }

    /**
     * @test
     * @depends it_can_be_instantiated
     * @param OrderItemsSubtotalCalculator $calculator
     */
    public function it_can_calculate_a_subtotal_if_there_are_no_order_items(
        OrderItemsSubtotalCalculator $calculator
    ): void {
        $subTotal = $calculator->getSubtotal($this->getOrderMock([]));
        $this->assertEquals(0, $subTotal);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
