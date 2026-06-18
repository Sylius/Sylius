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

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class HasEnabledEntity extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public ?string $objectManager = null,
        public string $message = 'Must have at least one enabled entity',
        public string $repositoryMethod = 'findBy',
        public ?string $errorPath = null,
        public string $enabledPath = 'enabled',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/core-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $this->objectManager = $options['objectManager'] ?? $this->objectManager;
            $this->message = $options['message'] ?? $this->message;
            $this->repositoryMethod = $options['repositoryMethod'] ?? $this->repositoryMethod;
            $this->errorPath = $options['errorPath'] ?? $this->errorPath;
            $this->enabledPath = $options['enabledPath'] ?? $this->enabledPath;
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
        return 'sylius_has_enabled_entity';
    }
}
