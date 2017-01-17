<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filtering;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface FiltersCriteriaResolverInterface
{
    /**
     * @param Grid $grid
     * @param Parameters $parameters
     *
     * @return bool
     */
    public function hasCriteria(Grid $grid, Parameters $parameters);

    /**
     * @param Grid $grid
     * @param Parameters $parameters
     *
     * @return array
     */
    public function getCriteria(Grid $grid, Parameters $parameters);
}
