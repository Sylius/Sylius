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

namespace Tests\Sylius\Component\Order\Aggregator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Aggregator\AdjustmentsByLabelAggregator;
use Sylius\Component\Order\Model\AdjustmentInterface;

final class AdjustmentsByLabelAggregatorTest extends TestCase
{
    private AdjustmentsByLabelAggregator $aggregator;

    private AdjustmentInterface&MockObject $adjustment1;

    private AdjustmentInterface&MockObject $adjustment2;

    private AdjustmentInterface&MockObject $adjustment3;

    private AdjustmentInterface&MockObject $adjustment4;

    public function setUp(): void
    {
        $this->aggregator = new AdjustmentsByLabelAggregator();
        $this->adjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment2 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment3 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment4 = $this->createMock(AdjustmentInterface::class);
    }

    public function testItImplementsAdjustmentsAggregatorInterface(): void
    {
        $this->assertInstanceOf(AdjustmentsAggregatorInterface::class, $this->aggregator);
    }

    public function testItAggregatesGivenAdjustmentsArrayByDescription(): void
    {
        $this->adjustment1->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 1');
        $this->adjustment1->expects($this->once())->method('getAmount')->willReturn(1000);

        $this->adjustment2->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 1');
        $this->adjustment2->expects($this->once())->method('getAmount')->willReturn(3000);

        $this->adjustment3->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 2');
        $this->adjustment3->expects($this->once())->method('getAmount')->willReturn(4000);

        $this->adjustment4->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 2');
        $this->adjustment4->expects($this->once())->method('getAmount')->willReturn(-2000);

        $result = $this->aggregator
            ->aggregate([
                $this->adjustment1,
                $this->adjustment2,
                $this->adjustment3,
                $this->adjustment4,
            ])
        ;

        $this->assertSame([
            'tax 1' => 4000,
            'tax 2' => 2000,
        ], $result);
    }

    public function testItThrowsExceptionIfAnyArrayElementIsNotAdjustment(): void
    {
        $this->adjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment2 = $this->createMock(AdjustmentInterface::class);

        $this->adjustment1->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 1');
        $this->adjustment1->expects($this->once())->method('getAmount')->willReturn(1000);

        $this->adjustment2->expects($this->atLeastOnce())->method('getLabel')->willReturn('tax 1');
        $this->adjustment2->expects($this->once())->method('getAmount')->willReturn(3000);

        $this->expectException(\InvalidArgumentException::class);

        $this->aggregator->aggregate([$this->adjustment1, $this->adjustment2, 'badObject']);
    }
}
