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
final class ChannelCodeCollection extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     * @param array<Constraint> $constraints
     * @param array<string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public ?string $extraFieldsMessage = null,
        public ?string $missingFieldsMessage = null,
        public string $invalidChannelMessage = 'sylius.channel_code_collection.invalid_channel',
        public array $constraints = [],
        public bool $allowExtraFields = false,
        public bool $allowMissingFields = false,
        public ?string $channelAwarePropertyPath = null,
        public bool $validateAgainstAllChannels = false,
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

            $this->constraints = $options['constraints'] ?? $this->constraints;
            $this->allowExtraFields = $options['allowExtraFields'] ?? $this->allowExtraFields;
            $this->allowMissingFields = $options['allowMissingFields'] ?? $this->allowMissingFields;
            $this->channelAwarePropertyPath = $options['channelAwarePropertyPath'] ?? $this->channelAwarePropertyPath;
            $this->extraFieldsMessage = $options['extraFieldsMessage'] ?? $this->extraFieldsMessage;
            $this->missingFieldsMessage = $options['missingFieldsMessage'] ?? $this->missingFieldsMessage;
            $this->invalidChannelMessage = $options['invalidChannelMessage'] ?? $this->invalidChannelMessage;
            $this->validateAgainstAllChannels = $options['validateAgainstAllChannels'] ?? $this->validateAgainstAllChannels;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);
    }

    /**
     * @param array<Constraint> $constraints
     */
    public function __construct(
        array $constraints = [],
        bool $allowExtraFields = false,
        bool $allowMissingFields = false,
        ?string $channelAwarePropertyPath = null,
        ?string $extraFieldsMessage = null,
        ?string $missingFieldsMessage = null,
        string $invalidChannelMessage = 'sylius.channel_code_collection.invalid_channel',
        bool $validateAgainstAllChannels = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->constraints = $constraints;
        $this->allowExtraFields = $allowExtraFields;
        $this->allowMissingFields = $allowMissingFields;
        $this->channelAwarePropertyPath = $channelAwarePropertyPath;
        $this->extraFieldsMessage = $extraFieldsMessage;
        $this->missingFieldsMessage = $missingFieldsMessage;
        $this->invalidChannelMessage = $invalidChannelMessage;
        $this->validateAgainstAllChannels = $validateAgainstAllChannels;
    }

    public function validatedBy(): string
    {
        return 'sylius_channel_code_collection';
    }
}
