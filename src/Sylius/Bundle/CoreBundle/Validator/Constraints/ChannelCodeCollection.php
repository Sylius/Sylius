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

final class ChannelCodeCollection extends Constraint
{
    /** @var array<Constraint> */
    public array $constraints = [];

    public bool $allowExtraFields = false;

    public bool $allowMissingFields = false;

    public ?string $channelAwarePropertyPath = null;

    public ?string $extraFieldsMessage = null;

    public ?string $missingFieldsMessage = null;

    public bool $validateAgainstAllChannels = false;

    public function validatedBy(): string
    {
        return 'sylius_channel_code_collection';
    }
}
