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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;

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
        $quantity = $this->accessor->getValue($value, $constraint->quantityPath);

        if (null === $stockable || null === $quantity) {
            return;
        }

        $isStockSufficient = $this->availabilityChecker->isStockSufficient($stockable, $quantity);

        if ($isStockSufficient) {
            return;
        }

        $this->context->addViolation(
            $constraint->message,
            array('%stockable%' => $stockable->getInventoryName())
        );
    }
}
