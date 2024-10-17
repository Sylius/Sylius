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

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidDateAttributeConfiguration extends Constraint
{
    public string $notBlank = 'sylius.attribute.date.configuration.format.not_blank';

    public string $invalidFormat = 'sylius.attribute.date.configuration.format.invalid';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_valid_date_attribute_validator';
    }
}
