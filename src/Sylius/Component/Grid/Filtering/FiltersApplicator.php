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

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class FiltersApplicator implements FiltersApplicatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $filtersRegistry;

    /**
     * @var FiltersCriteriaResolverInterface
     */
    private $criteriaResolver;

    public function __construct(
        ServiceRegistryInterface $filtersRegistry,
        FiltersCriteriaResolverInterface $criteriaResolver
    ) {
        $this->filtersRegistry = $filtersRegistry;
        $this->criteriaResolver = $criteriaResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, Grid $grid, Parameters $parameters): void
    {
        if (!$this->criteriaResolver->hasCriteria($grid, $parameters)) {
            return;
        }

        $criteria = $this->criteriaResolver->getCriteria($grid, $parameters);
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
}
