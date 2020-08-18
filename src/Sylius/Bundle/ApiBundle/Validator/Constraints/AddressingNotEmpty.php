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

final class AddressingNotEmpty extends Constraint
{
    /** @var string */
    public $message = 'sylius.addressing.not_blank';

    public function validatedBy()
    {
        return 'sylius.validator.addressing_not_empty';
    }
}
