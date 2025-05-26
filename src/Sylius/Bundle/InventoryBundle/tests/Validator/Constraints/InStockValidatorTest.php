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

namespace Tests\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStockValidator;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Validator\Context\ExecutionContext;

final class InStockValidatorTest extends TestCase
{
    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private ExecutionContext&MockObject $context;

    private InStockValidator $inStockValidator;

    protected function setUp(): void
    {
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->context = $this->createMock(ExecutionContext::class);

        $this->inStockValidator = new InStockValidator($this->availabilityChecker);
        $this->inStockValidator->initialize($this->context);
    }

    public function testDoesNotAddViolationIfThereIsNoStockable(): void
    {
        /** @var InventoryUnitInterface&MockObject $inventoryUnit */
        $inventoryUnit = $this->createMock(InventoryUnitInterface::class);
        $inventoryUnit->method('getStockable')->willReturn(null);

        $this->context->expects($this->never())->method('addViolation');

        $this->inStockValidator->validate($inventoryUnit, new InStock());
    }

    public function testDoesNotAddViolationWhenValidatingNumberAndThereIsNoStockable(): void
    {
        /** @var InventoryUnitInterface&MockObject $inventoryUnit */
        $inventoryUnit = $this->createMock(InventoryUnitInterface::class);
        $inventoryUnit->method('getStockable')->willReturn(null);

        $this->context->expects($this->once())->method('getObject')->willReturn($inventoryUnit);
        $this->context->expects($this->never())->method('addViolation');

        $this->inStockValidator->validate(1, new InStock());
    }

    public function testDoesNotAddViolationIfThereIsNoQuantity(): void
    {
        /** @var StockableInterface&MockObject $stockable */
        $stockable = $this->createMock(StockableInterface::class);

        $inventoryUnit = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getQuantity', 'getStockable'])
            ->getMock();
        $inventoryUnit->method('getStockable')->willReturn($stockable);
        $inventoryUnit->method('getQuantity')->willReturn(null);

        $this->context->expects($this->never())->method('addViolation');

        $this->inStockValidator->validate($inventoryUnit, new InStock());
    }

    public function testAddsViolationWhenValidatingNumberAndThereIsNoQuantity(): void
    {
        /** @var StockableInterface&MockObject $stockable */
        $stockable = $this->createMock(StockableInterface::class);
        /** @var InventoryUnitInterface&MockObject $inventoryUnit */
        $inventoryUnit = $this->createMock(InventoryUnitInterface::class);
        $inventoryUnit->method('getStockable')->willReturn($stockable);

        $this->context->expects($this->once())->method('getObject')->willReturn($inventoryUnit);
        $this->context->expects($this->once())->method('addViolation');

        $this->inStockValidator->validate(1, new InStock());
    }

    public function testDoesNotAddViolationIfStockIsSufficient(): void
    {
        /** @var StockableInterface&MockObject $stockable */
        $stockable = $this->createMock(StockableInterface::class);

        $inventoryUnit = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getQuantity', 'getStockable'])
            ->getMock();
        $inventoryUnit->method('getStockable')->willReturn($stockable);
        $inventoryUnit->method('getQuantity')->willReturn(1);

        $this->availabilityChecker->expects($this->once())->method('isStockSufficient')->with($stockable, 1)->willReturn(true);
        $this->context->expects($this->never())->method('addViolation');

        $this->inStockValidator->validate($inventoryUnit, new InStock());
    }

    public function testDoesNotAddViolationWhenValidatingNumberAndStockIsSufficient(): void
    {
        /** @var StockableInterface&MockObject $stockable */
        $stockable = $this->createMock(StockableInterface::class);
        /** @var InventoryUnitInterface&MockObject $inventoryUnit */
        $inventoryUnit = $this->createMock(InventoryUnitInterface::class);
        $inventoryUnit->method('getStockable')->willReturn($stockable);

        $this->availabilityChecker
            ->expects($this->once())->method('isStockSufficient')
            ->with($stockable, 1)
            ->willReturn(true)
        ;

        $this->context->expects($this->once())->method('getObject')->willReturn($inventoryUnit);
        $this->context->expects($this->never())->method('addViolation');

        $this->inStockValidator->validate(1, new InStock());
    }
}
