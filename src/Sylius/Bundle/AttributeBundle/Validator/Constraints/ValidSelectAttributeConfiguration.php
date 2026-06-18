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
final class ValidSelectAttributeConfiguration extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $messageMultiple = 'sylius.attribute.configuration.multiple',
        public string $messageMinEntries = 'sylius.attribute.configuration.min_entries',
        public string $messageMaxEntries = 'sylius.attribute.configuration.max_entries',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/attribute-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $this->messageMultiple = $options['messageMultiple'] ?? $this->messageMultiple;
            $this->messageMinEntries = $options['messageMinEntries'] ?? $this->messageMinEntries;
            $this->messageMaxEntries = $options['messageMaxEntries'] ?? $this->messageMaxEntries;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_valid_select_attribute_validator';
    }
}
