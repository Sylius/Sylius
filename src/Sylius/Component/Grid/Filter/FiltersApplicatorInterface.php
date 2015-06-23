<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\DataSource\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FiltersApplicatorInterface
{
    /**
     * @param Grid                $grid
     * @param DataSourceInterface $dataSource
     * @param Parameters          $parameters
     * @param $data
     */
    public function apply(Grid $grid, DataSourceInterface $dataSource, Parameters $parameters);
}
