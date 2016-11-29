<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Exception;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class InsufficientStockException extends \UnderflowException
{
    /**
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * @param StockableInterface $stockable
     * @param int $quantity
     * @param \Exception|null $previousException
     */
    public function __construct(StockableInterface $stockable, $quantity, \Exception $previousException = null)
    {
        $this->stockable = $stockable;

        parent::__construct(sprintf(
            'Only %d %s(s) on hand, %d requested.',
            $stockable->getOnHand(),
            $stockable->getInventoryName(),
            $quantity
        ), 0, $previousException);
    }

    /**
     * @return StockableInterface
     */
    public function getStockable()
    {
        return $this->stockable;
    }
}
