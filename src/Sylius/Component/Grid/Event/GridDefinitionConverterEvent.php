<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Event;

use Sylius\Component\Grid\Definition\Grid;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class GridDefinitionConverterEvent extends Event
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }
}
