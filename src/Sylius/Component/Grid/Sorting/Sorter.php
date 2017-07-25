<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Sorting;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Sorter implements SorterInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $sorting = $parameters->get('sorting', $grid->getSorting());

        foreach ($sorting as $field => $order) {
            $gridField = $grid->getField($field);
            $property = $gridField->getSortable();

            if (null !== $property) {
                $expressionBuilder->addOrderBy($property, $order);
            }
        }
    }
}
