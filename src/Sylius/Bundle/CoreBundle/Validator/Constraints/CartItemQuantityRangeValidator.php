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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\RangeValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CartItemQuantityRangeValidator extends RangeValidator
{
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        private readonly int $orderItemQuantityModifierLimit,
    ) {
        parent::__construct($propertyAccessor);
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CartItemQuantityRange) {
            throw new UnexpectedTypeException($constraint, CartItemQuantityRange::class);
        }

        $constraint->max = $this->orderItemQuantityModifierLimit;

        parent::validate($value, $constraint);
    }
}
