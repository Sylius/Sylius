<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantCombination extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.product_variant.combination';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius.validator.product_variant_combination';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
