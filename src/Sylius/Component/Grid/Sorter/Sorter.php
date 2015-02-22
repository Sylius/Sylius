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
class Sorter implements SorterInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort(Grid $grid, DataSourceInterface $dataSource, Parameters $parameters)
    {
        $sorting = $parameters->get('sorting', $grid->getSorting());

        foreach ($sorting as $column => $direction) {
            if (!$grid->hasColumn($column) || !$grid->getColumn($column)->isSortable()) {
                continue;
            }

            if (in_array($direction, array(SorterInterface::ASC, SorterInterface::DESC))) {
                $dataSource->getExpressionBuilder()->orderBy($column, $direction);
            }
        }
    }
}
