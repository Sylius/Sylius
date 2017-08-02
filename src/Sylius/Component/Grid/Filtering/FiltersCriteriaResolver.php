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

namespace Sylius\Component\Grid\Filtering;

use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class FiltersCriteriaResolver implements FiltersCriteriaResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasCriteria(Grid $grid, Parameters $parameters)
    {
        return $parameters->has('criteria') || !empty($this->getFiltersDefaultCriteria($grid->getFilters()));
    }

    /**
     * {@inheritdoc}
     */
    public function getCriteria(Grid $grid, Parameters $parameters)
    {
        $defaultCriteria = array_map(function (Filter $filter) {
            return $filter->getCriteria();
        }, $this->getFiltersDefaultCriteria($grid->getFilters()));

        return $parameters->get('criteria', $defaultCriteria);
    }

    /**
     * @param Filter[] $filters
     *
     * @return Filter[]
     */
    private function getFiltersDefaultCriteria(array $filters)
    {
        return array_filter($filters, function (Filter $filter) {
            return null !== $filter->getCriteria();
        });
    }
}
