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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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

            $options = $grid->getColumn($column)->getOptions();

            $field = isset($options['path']) ? $options['path'] : $column;
            $field = isset($options['sort']) ? $options['sort'] : $field;

            if (in_array($direction, array(SorterInterface::ASC, SorterInterface::DESC))) {
                $this->applySorting($dataSource, $field, $direction);
            }
        }
    }

    /**
     * @param DataSourceInterface $dataSource
     * @param string|array        $sortingFields
     * @param string              $direction
     */
    private function applySorting(DataSourceInterface $dataSource, $sortingFields, $direction)
    {
        if (is_array($sortingFields)) {
            foreach ($sortingFields as $field) {
                $dataSource->getExpressionBuilder()->addOrderBy($field, $direction);
            }

            return;
        }

        $dataSource->getExpressionBuilder()->orderBy($sortingFields, $direction);
    }
}
