<?php

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class AttributeType extends Constraint
{
    public string $unregisteredAttributeTypeMessage = 'sylius.attribute.type.unregistered';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_attribute_type_validator';
    }
}
