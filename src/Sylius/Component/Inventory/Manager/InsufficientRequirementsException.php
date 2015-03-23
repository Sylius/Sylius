<?php

/**
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
class InsufficientRequirementsException extends \UnderflowException
{
    /**
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * @var Message string
     */
    protected $message = 'Only %d %s(s) on hand, %d requested.';

    /**
     * @var Mark value
     */
    protected $mark = null;

    /**
     * @param StockableInterface $stockable
     * @param integer            $quantity
     */
    public function __construct(StockableInterface $stockable, $quantity)
    {
        $this->stockable = $stockable;

        if (null === $this->mark)
            $this->mark = $stockable->getStock()->getOnHand();

        parent::__construct($this->getText($quantity));
    }

    /**
     * @return StockableInterface
     */
    public function getStockable()
    {
        return $this->stockable;
    }

    /** 
     * Returns error message
     */
    public function getText($quantity)
    {
        return sprintf(
            $this->message,
            $this->mark,
            $this->stockable->getInventoryName(),
            $quantity
        );        
    }
}
