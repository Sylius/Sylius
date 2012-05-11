<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\EventDispatcher\Event;

use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Filter stockable event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterStockableEvent extends Event
{
    /**
     * Stockable object.
     *
     * @var StockableInterface
     */
    private $stockable;

    /**
     * Constructor.
     *
     * @param StockableInterface $stockable
     */
    public function __construct(StockableInterface $stockable)
    {
        $this->stockable = $stockable;
    }

    /**
     * Get stockable.
     *
     * @return StockableInterface
     */
    public function getStockable()
    {
        return $this->stockable;
    }
}
