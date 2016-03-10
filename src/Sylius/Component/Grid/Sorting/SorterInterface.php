<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Sorting;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SorterInterface
{
    /**
     * @param DataSourceInterface $dataSource
     * @param Grid $grid
     * @param Parameters $parameters
     */
    public function sort(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters);
}
