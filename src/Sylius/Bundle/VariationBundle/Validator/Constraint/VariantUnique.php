<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Unique variant property constraint.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 *
 * @Annotation
 */
class VariantUnique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This property must be unique';

    /**
     * @var mixed
     */
    public $property;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius.validator.variant.unique';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
