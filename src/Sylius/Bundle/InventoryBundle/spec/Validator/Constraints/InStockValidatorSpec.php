<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStockValidator;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\ConstraintValidator;

final class InStockValidatorSpec extends ObjectBehavior
{
    function let(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->beConstructedWith($availabilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InStockValidator::class);
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_not_add_violation_if_there_is_no_stockable(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor
    ) {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn(null);

        $constraint = new InStock();

        $this->validate($inventoryUnit, $constraint);
    }

    function it_does_not_add_violation_if_there_is_no_quantity(
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable
    ) {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(null);

        $constraint = new InStock();

        $this->validate($inventoryUnit, $constraint);
    }

    function it_does_not_add_violation_if_stock_is_sufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        InventoryUnitInterface $inventoryUnit,
        PropertyAccessor $propertyAccessor,
        StockableInterface $stockable
    ) {
        $propertyAccessor->getValue($inventoryUnit, 'stockable')->willReturn($stockable);
        $propertyAccessor->getValue($inventoryUnit, 'quantity')->willReturn(1);

        $availabilityChecker->isStockSufficient($stockable, 1)->willReturn(true);

        $constraint = new InStock();

        $this->validate($inventoryUnit, $constraint);
    }
}
