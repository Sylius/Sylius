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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ZoneCannotContainItself extends Constraint
{
    /** @var string */
    public $message = 'sylius.zone_member.unique';

    public function validatedBy(): string
    {
        return 'sylius.validator.zone_cannot_contain_itself';
    }
}
