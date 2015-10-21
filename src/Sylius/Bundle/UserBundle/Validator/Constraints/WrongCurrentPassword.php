<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class WrongCurrentPassword extends Constraint
{
    public $message = 'sylius.user.plainPassword.wrong_current';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'wrong_current_password_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
