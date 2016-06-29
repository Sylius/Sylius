<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Grid\Sorting;

use Sylius\Grid\Data\DataSourceInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Sorter implements SorterInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $sorting = $parameters->has('sorting') ? $parameters->get('sorting') : $grid->getSorting();

        foreach ($sorting as $field => $direction) {
            $expressionBuilder->addOrderBy($field, $direction);
        }
    }
}
