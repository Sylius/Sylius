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

use Sylius\Component\Core\Model\OrderItemInterface;
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

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var InStock $constraint */
        Assert::isInstanceOf($constraint, InStock::class);

        $target = is_int($value) ? Constraint::PROPERTY_CONSTRAINT : Constraint::CLASS_CONSTRAINT;
        /** @var OrderItemInterface $object */
        $object = Constraint::PROPERTY_CONSTRAINT === $target ? $this->context->getObject() : $value;

        $stockable = $this->accessor->getValue($object, $constraint->stockablePath);
        if (null === $stockable) {
            return;
        }

        $quantity = Constraint::CLASS_CONSTRAINT === $target ? $this->accessor->getValue($object, $constraint->quantityPath) : $value;
        if (null === $quantity) {
            return;
        }

        if ($this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            return;
        }

        if (Constraint::CLASS_CONSTRAINT === $target) {
            $this->context->addViolation(
                $constraint->message,
                ['%itemName%' => $stockable->getInventoryName()],
            );

            return;
        }

        $this->context->addViolation($constraint->shortMessage);
    }
}
