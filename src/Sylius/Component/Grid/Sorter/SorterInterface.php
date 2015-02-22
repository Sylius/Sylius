<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Sorter;

use Sylius\Component\Grid\DataSource\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SorterInterface
{
    const ASC  = 'asc';
    const DESC = 'desc';

    /**
     * @param Grid $grid
     * @param DataSourceInterface $dataSource
     * @param Parameters $parameters
     */
    public function sort(Grid $grid, DataSourceInterface $dataSource, Parameters $parameters);
}
