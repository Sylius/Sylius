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

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;

final class InStockValidatorSpec extends ObjectBehavior
{
    function let(
        AvailabilityCheckerInterface $availabilityChecker,
        ExecutionContext $context,
    ): void {
        $this->beConstructedWith($availabilityChecker);
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_not_add_violation_if_there_is_no_stockable(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
    ): void {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn(null);

        $this->validate($inventoryUnit, new InStock());
    }

    function it_does_not_add_violation_when_validating_number_and_there_is_no_stockable(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        ExecutionContext $context,
    ): void {
        $context->getObject()->willReturn($inventoryUnit);
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn(null);

        $this->validate(1, new InStock());
    }

    function it_does_not_add_violation_if_there_is_no_quantity(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable,
    ): void {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(null);

        $this->validate($inventoryUnit, new InStock());
    }

    function it_does_not_add_violation_when_validating_number_and_there_is_no_quantity(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable,
        ExecutionContext $context,
    ): void {
        $context->getObject()->willReturn($inventoryUnit);
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(null);

        $this->validate(1, new InStock());
    }

    function it_does_not_add_violation_if_stock_is_sufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable,
    ): void {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(1);

        $availabilityChecker->isStockSufficient($stockable, 1)->willReturn(true);

        $this->validate($inventoryUnit, new InStock());
    }

    function it_does_not_add_violation_when_validating_number_and_stock_is_sufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable,
        ExecutionContext $context,
    ): void {
        $context->getObject()->willReturn($inventoryUnit);
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(2);

        $availabilityChecker->isStockSufficient($stockable, 1)->willReturn(true);

        $this->validate(1, new InStock());
    }
}
