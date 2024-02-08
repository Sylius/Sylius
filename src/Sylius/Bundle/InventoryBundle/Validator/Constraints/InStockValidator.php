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

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class InStockValidator extends ConstraintValidator
{
    private PropertyAccessor $accessor;

    public function __construct(private AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var InStock $constraint */
        Assert::isInstanceOf($constraint, InStock::class);

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
                ['%itemName%' => $stockable->getInventoryName()],
            );
        }
    }
}
