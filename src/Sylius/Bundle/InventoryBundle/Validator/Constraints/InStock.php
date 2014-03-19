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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InStock extends Constraint
{
    public $message = '%stockable% does not have sufficient stock.';
    public $stockablePath = 'stockable';
    public $quantityPath = 'quantity';

    public function validatedBy()
    {
        return 'sylius_in_stock';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
