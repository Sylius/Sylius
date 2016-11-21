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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class UniqueSimpleProductCode extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.simple_product.code.unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius.validator.unique_simple_product_code';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
