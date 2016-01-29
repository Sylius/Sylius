<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InStockValidator extends ConstraintValidator
{
    protected $availabilityChecker;
    protected $accessor;

    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function validate($value, Constraint $constraint)
    {
        $stockable = $this->accessor->getValue($value, $constraint->stockablePath);
        if (null === $stockable) {
            return;
        }

        $quantity = $this->accessor->getValue($value, $constraint->quantityPath);
        if (null === $quantity) {
            return;
        }

        if (!$this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $stockable->getInventoryName()]
            );
        }
    }
}
