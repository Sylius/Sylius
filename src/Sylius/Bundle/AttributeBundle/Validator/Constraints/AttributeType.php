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

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AttributeType extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $unregisteredAttributeTypeMessage = 'sylius.attribute.type.unregistered',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->unregisteredAttributeTypeMessage = $unregisteredAttributeTypeMessage;
    }

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
