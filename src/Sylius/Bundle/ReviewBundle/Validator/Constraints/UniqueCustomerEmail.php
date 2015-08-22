<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class UniqueCustomerEmail extends Constraint
{
    public $message = 'sylius.review.author.already_exists';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'unique_customer_email_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
