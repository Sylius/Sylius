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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class InStock extends Constraint
{
    /**
     * @var string
     */
    public $message = '%stockable% does not have sufficient stock.';

    /**
     * @var string
     */
    public $stockablePath = 'stockable';

    /**
     * @var string
     */
    public $quantityPath = 'quantity';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_in_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
