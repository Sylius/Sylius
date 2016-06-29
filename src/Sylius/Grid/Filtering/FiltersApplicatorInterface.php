<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Grid\Filtering;

use Sylius\Grid\Data\DataSourceInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FiltersApplicatorInterface
{
    /**
     * @param DataSourceInterface $dataSource
     * @param Grid $grid
     * @param Parameters $parameters
     */
    public function apply(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters);
}
