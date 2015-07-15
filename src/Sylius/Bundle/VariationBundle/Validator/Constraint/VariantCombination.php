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
 * Unique option values combination for variant constraint.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 *
 * @Annotation
 */
class VariantCombination extends Constraint
{
    public $message = 'sylius.variant.combination';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius.validator.variant.combination';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
