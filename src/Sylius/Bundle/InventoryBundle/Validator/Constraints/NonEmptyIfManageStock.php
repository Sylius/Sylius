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
class NonEmptyIfManageStock extends Constraint
{
    public $message = 'Must not be an empty value';
    public $fields = array();
    
    public function validatedBy()
    {
        return 'sylius_stock_non_empty_if_manage_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}