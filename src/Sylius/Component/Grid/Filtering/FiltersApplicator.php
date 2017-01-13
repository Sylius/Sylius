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

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FiltersApplicator implements FiltersApplicatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $filtersRegistry;

    /**
     * @param ServiceRegistryInterface $filtersRegistry
     */
    public function __construct(ServiceRegistryInterface $filtersRegistry)
    {
        $this->filtersRegistry = $filtersRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters)
    {
        if (!$parameters->has('criteria') && !$this->hasDefaultFilteringCriteria($grid)) {
            return;
        }

        $criteria = $parameters->get('criteria', $this->getDefaultFilteringCriteria($grid));
        foreach ($criteria as $name => $data) {
            if (!$grid->hasFilter($name)) {
                continue;
            }

            $gridFilter = $grid->getFilter($name);

            /** @var FilterInterface $filter */
            $filter = $this->filtersRegistry->get($gridFilter->getType());
            $filter->apply($dataSource, $name, $data, $gridFilter->getOptions());
        }
    }

    /**
     * @param Grid $grid
     *
     * @return bool
     */
    private function hasDefaultFilteringCriteria(Grid $grid)
    {
        return !empty($this->getFiltersWithDefaultCriteria($grid->getFilters()));
    }

    /**
     * @param Grid $grid
     *
     * @return Filter[]
     */
    private function getDefaultFilteringCriteria(Grid $grid)
    {
        $filters = $this->getFiltersWithDefaultCriteria($grid->getFilters());

        return array_map(function (Filter $filter) {
            return $filter->getCriteria();
        }, $filters);
    }

    /**
     * @param Filter[] $filters
     *
     * @return Filter[]
     */
    private function getFiltersWithDefaultCriteria(array $filters)
    {
        return array_filter($filters, function (Filter $filter) {
            return !empty($filter->getCriteria());
        });
    }
}
