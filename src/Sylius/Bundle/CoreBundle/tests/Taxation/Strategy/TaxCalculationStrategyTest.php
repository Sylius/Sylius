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

namespace Tests\Sylius\Bundle\CoreBundle\Taxation\Strategy;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;

final class TaxCalculationStrategyTest extends TestCase
{
    private MockObject&OrderTaxesApplicatorInterface $applicatorOne;

    private MockObject&OrderTaxesApplicatorInterface $applicatorTwo;

    private TaxCalculationStrategy $strategy;

    protected function setUp(): void
    {
        $this->applicatorOne = $this->createMock(OrderTaxesApplicatorInterface::class);
        $this->applicatorTwo = $this->createMock(OrderTaxesApplicatorInterface::class);

        $this->strategy = new TaxCalculationStrategy('order_items_based', [
            $this->applicatorOne,
            $this->applicatorTwo,
        ]);
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(TaxCalculationStrategyInterface::class, $this->strategy);
    }

    public function testHasAType(): void
    {
        $this->assertSame('order_items_based', $this->strategy->getType());
    }

    public function testThrowsExceptionIfApplicatorNotInstanceOfCorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TaxCalculationStrategy('order_items_based', [
            $this->applicatorOne,
            new \stdClass(),
        ]);
    }

    public function testIsSupportedWhenChannelStrategyMatches(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $zone = $this->createMock(ZoneInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $order->method('getChannel')->willReturn($channel);
        $channel->method('getTaxCalculationStrategy')->willReturn('order_items_based');

        $this->assertTrue($this->strategy->supports($order, $zone));
    }

    public function testIsNotSupportedWhenChannelStrategyDoesNotMatch(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $zone = $this->createMock(ZoneInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $order->method('getChannel')->willReturn($channel);
        $channel->method('getTaxCalculationStrategy')->willReturn('order_item_units_based');

        $this->assertFalse($this->strategy->supports($order, $zone));
    }

    public function testAppliesAllApplicators(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $zone = $this->createMock(ZoneInterface::class);

        $this->applicatorOne->expects($this->once())->method('apply')->with($order, $zone);
        $this->applicatorTwo->expects($this->once())->method('apply')->with($order, $zone);

        $this->strategy->applyTaxes($order, $zone);
    }
}
