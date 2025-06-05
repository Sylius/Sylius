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

namespace Tests\Sylius\Component\Order\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class AdjustmentFactoryTest extends TestCase
{
    /** @var FactoryInterface<AdjustmentInterface>&MockObject */
    private FactoryInterface&MockObject $adjustmentFactoryMock;

    /** @var AdjustmentFactory<AdjustmentInterface> */
    private AdjustmentFactory $adjustmentFactory;

    private AdjustmentInterface&MockObject $adjustment;

    protected function setUp(): void
    {
        $this->adjustmentFactoryMock = $this->createMock(FactoryInterface::class);
        $this->adjustmentFactory = new AdjustmentFactory($this->adjustmentFactoryMock);
        $this->adjustment = $this->createMock(AdjustmentInterface::class);
    }

    public function testImplementsAnAdjustmentFactoryInterface(): void
    {
        $this->assertInstanceOf(AdjustmentFactoryInterface::class, $this->adjustmentFactory);
    }

    public function testCreatesNewAdjustment(): void
    {
        $this->adjustmentFactoryMock->expects($this->once())->method('createNew')->willReturn($this->adjustment);
        $this->assertSame($this->adjustment, $this->adjustmentFactory->createNew());
    }

    public function testCreatesNewAdjustmentWithProvidedData(): void
    {
        $this->adjustmentFactoryMock->expects($this->once())->method('createNew')->willReturn($this->adjustment);

        $this->adjustment->expects($this->once())->method('setType')->with('tax');
        $this->adjustment->expects($this->once())->method('setLabel')->with('Tax description');
        $this->adjustment->expects($this->once())->method('setAmount')->with(1000);
        $this->adjustment->expects($this->once())->method('setNeutral')->with(false);
        $this->adjustment->expects($this->once())->method('setDetails')->with(['taxRateAmount' => 0.1]);

        $result = $this->adjustmentFactory->createWithData(
            'tax',
            'Tax description',
            1000,
            false,
            ['taxRateAmount' => 0.1],
        );

        $this->assertSame($this->adjustment, $result);
    }
}
