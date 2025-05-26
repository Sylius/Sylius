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

namespace Tests\Sylius\Component\Inventory\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Inventory\Checker\AvailabilityChecker;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

final class AvailabilityCheckerTest extends TestCase
{
    private AvailabilityChecker $availabilityChecker;

    /** @var StockableInterface&MockObject */
    private StockableInterface $stockable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityChecker = new AvailabilityChecker();
        $this->stockable = $this->createMock(StockableInterface::class);
    }

    public function testShouldImplementAvailabilityCheckerInterface(): void
    {
        self::assertInstanceOf(AvailabilityCheckerInterface::class, $this->availabilityChecker);
    }

    public function testRecognizeStockableAsAvailableIfOnHandQuantityIsGreaterThan0(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(5);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(0);

        self::assertTrue($this->availabilityChecker->isStockAvailable($this->stockable));
    }

    public function testRecognizeStockableAsNotAvailableIfOnHandQuantityIsEqualTo0(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(0);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(0);

        self::assertFalse($this->availabilityChecker->isStockAvailable($this->stockable));
    }

    public function testRecognizeStockableAsAvailableIfOnHoldQuantityIsLessThanOnHand(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(5);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(4);

        self::assertTrue($this->availabilityChecker->isStockAvailable($this->stockable));
    }

    public function testRecognizeStockableAsNotAvailableIfOnHoldQuantityIsSameAsOnHand(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(5);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(5);

        self::assertFalse($this->availabilityChecker->isStockAvailable($this->stockable));
    }

    public function testRecognizeStockableAsSufficientIfOnHandMinusOnHoldQuantityIsGreaterThanTheRequiredQuantity(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(10);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(3);

        self::assertTrue($this->availabilityChecker->isStockSufficient($this->stockable, 5));
    }

    public function testRecognizeStockableAsSufficientIfOnHandMinusOnHoldQuantityIsEqualToTheRequiredQuantity(): void
    {
        $this->stockable->expects(self::once())->method('isTracked')->willReturn(true);
        $this->stockable->expects(self::once())->method('getOnHand')->willReturn(10);
        $this->stockable->expects(self::once())->method('getOnHold')->willReturn(5);

        self::assertTrue($this->availabilityChecker->isStockSufficient($this->stockable, 5));
    }

    public function testRecognizeStockableAsAvailableOrSufficientIfItIsNotTracked(): void
    {
        $this->stockable->expects(self::exactly(2))->method('isTracked')->willReturn(false);

        self::assertTrue($this->availabilityChecker->isStockAvailable($this->stockable));
        self::assertTrue($this->availabilityChecker->isStockSufficient($this->stockable, 42));
    }
}
