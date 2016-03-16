<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Operator;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Thrown when decreasing stockable quantity while it is insufficient.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InsufficientStockException extends \UnderflowException
{
    /**
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * @param StockableInterface $stockable
     * @param int            $quantity
     */
    public function __construct(StockableInterface $stockable, $quantity)
    {
        $this->stockable = $stockable;

        parent::__construct(sprintf(
            'Only %d %s(s) on hand, %d requested.',
            $stockable->getOnHand(),
            $stockable->getInventoryName(),
            $quantity
        ));
    }

    /**
     * @return StockableInterface
     */
    public function getStockable()
    {
        return $this->stockable;
    }
}
