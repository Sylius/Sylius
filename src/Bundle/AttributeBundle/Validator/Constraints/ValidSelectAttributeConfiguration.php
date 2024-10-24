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

final class ValidSelectAttributeConfiguration extends Constraint
{
    public string $messageMultiple = 'sylius.attribute.configuration.multiple';

    public string $messageMinEntries = 'sylius.attribute.configuration.min_entries';

    public string $messageMaxEntries = 'sylius.attribute.configuration.max_entries';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_valid_select_attribute_validator';
    }
}
