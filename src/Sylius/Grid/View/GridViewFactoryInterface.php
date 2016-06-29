<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Grid\View;

use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface GridViewFactoryInterface
{
    /**
     * @param Grid $grid
     * @param Parameters $parameters
     *
     * @return GridView
     */
    public function create(Grid $grid, Parameters $parameters);
}
