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

namespace Sylius\Bundle\ShippingBundle\Validator;

use Sylius\Bundle\ShippingBundle\Validator\Constraint\ValidDeliveryTimeRange;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidDeliveryTimeRangeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidDeliveryTimeRange) {
            throw new UnexpectedTypeException($constraint, ValidDeliveryTimeRange::class);
        }

        if (!$value instanceof ShippingMethodInterface) {
            throw new UnexpectedValueException($value, ShippingMethodInterface::class);
        }

        $min = $value->getMinDeliveryTimeDays();
        $max = $value->getMaxDeliveryTimeDays();

        if ($min !== null && $max !== null && $max < $min) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('maxDeliveryTimeDays')
                ->addViolation()
            ;
        }
    }
}
