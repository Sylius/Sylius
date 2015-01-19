<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Myke Hines <myke@webhines.com>
 */
class MaxQuantityGreaterThenOrEqualMinQuantity extends Constraint
{
    public $message = 'Maximum quantity in cart should be greater than or equal to minimum quantity in cart.';

    public function validatedBy()
    {
        return 'sylius_stock_maxQuantity';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}