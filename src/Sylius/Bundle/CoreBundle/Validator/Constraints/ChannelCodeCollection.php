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

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ChannelCodeCollection extends Constraint
{
    /** @var array<Constraint> */
    public array $constraints = [];

    public bool $allowExtraFields = false;

    public bool $allowMissingFields = false;

    public ?string $channelAwarePropertyPath = null;

    public ?string $extraFieldsMessage = null;

    public ?string $missingFieldsMessage = null;

    public string $invalidChannelMessage = 'sylius.channel_code_collection.invalid_channel';

    public bool $validateAgainstAllChannels = false;

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
