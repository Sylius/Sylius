<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to require an address to be shippable.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ShippableAddressConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.address.not_shippable';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_shippable_address_validator';
    }
}
