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

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class InStock extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.cart_item.not_available';

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
    public function validatedBy(): string
    {
        return 'sylius_in_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
