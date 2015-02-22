<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\DataSource;

use Sylius\Component\Grid\Definition\Grid;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DataSourceProviderInterface
{
    /**
     * @param Grid $grid
     *
     * @return DataSourceInterface
     */
    public function getDataSource(Grid $grid);
}
