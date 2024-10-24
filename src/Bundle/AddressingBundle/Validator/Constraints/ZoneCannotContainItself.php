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

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ZoneCannotContainItself extends Constraint
{
    public string $message = 'sylius.zone_member.cannot_be_the_same_as_zone';

    public function validatedBy(): string
    {
        return 'sylius_zone_cannot_contain_itself_validator';
    }
}
