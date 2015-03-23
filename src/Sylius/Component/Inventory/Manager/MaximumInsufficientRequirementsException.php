<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Manager;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Thrown when order inventory requirements is not met
 *
 * @author Myke Hines <myke@webhines.com>
 */
class MaximumInsufficientRequirementsException extends InsufficientRequirementsException
{
    /**
     * @var Message string
     */
    protected $message = 'Maximum units (%d) for %s not met, %d requested.';

    /**
     * @param StockableInterface $stockable
     * @param integer            $quantity
     */
    public function __construct(StockableInterface $stockable, $quantity, $mark)
    {
        $this->mark = $mark;

        parent::__construct($stockable, $quantity);
    }    
}
