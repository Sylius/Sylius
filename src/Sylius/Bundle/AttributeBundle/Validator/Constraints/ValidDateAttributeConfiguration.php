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
final class ValidDateAttributeConfiguration extends Constraint
{
    public const AVAILABLE_FORMATS = [
        'none',
        'short',
        'medium',
        'long',
        'full',
        'relative_short',
        'relative_medium',
        'relative_long',
        'relative_full',
    ];

    #[HasNamedArguments]
    public function __construct(
        public string $message = 'sylius.attribute.configuration.format.invalid',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_valid_date_attribute_validator';
    }
}
