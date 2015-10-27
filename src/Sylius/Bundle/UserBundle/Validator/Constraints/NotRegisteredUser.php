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
class NotRegisteredUser extends Constraint
{
    public $message = 'This email is not registered. Please register first.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'not_registered_user_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
