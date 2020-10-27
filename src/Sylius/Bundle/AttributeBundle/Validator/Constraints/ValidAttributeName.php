<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidAttributeName extends Constraint
{
    /** @var string */
    public $messageAttributeNameNotBlank = 'sylius.attribute.name.not_blank';

    /** @var string */
    public $messageAttributeNotTranslatableNameNotBlank = 'sylius.attribute.not_translatable_name.not_blank';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_valid_attribute_name_validator';
    }
}
