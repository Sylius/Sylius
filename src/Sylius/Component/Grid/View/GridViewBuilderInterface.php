<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface GridViewBuilderInterface
{
    /**
     * @param Grid       $grid
     * @param Parameters $parameters
     *
     * @return GridView
     */
    public function build(Grid $grid, Parameters $parameters);
}
