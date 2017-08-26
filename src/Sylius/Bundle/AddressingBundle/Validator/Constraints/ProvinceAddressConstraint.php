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

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to require a province to be valid.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProvinceAddressConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.address.province.valid';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_province_address_validator';
    }
}
