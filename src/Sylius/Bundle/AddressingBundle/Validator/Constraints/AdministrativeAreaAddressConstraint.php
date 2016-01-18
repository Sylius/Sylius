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
 * Constraint to require a province to be valid.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AdministrativeAreaAddressConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.address.administrative_area.valid';

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
        return 'sylius_administrative_area_address_validator';
    }
}
