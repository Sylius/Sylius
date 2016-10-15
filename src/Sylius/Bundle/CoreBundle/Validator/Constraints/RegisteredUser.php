<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class RegisteredUser extends Constraint
{
    public $message = 'This email is already registered. Please log in.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'registered_user_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
