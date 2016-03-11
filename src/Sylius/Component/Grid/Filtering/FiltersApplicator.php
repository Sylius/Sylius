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
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FiltersApplicator implements FiltersApplicatorInterface
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
        if (!$parameters->has('criteria')) {
            return;
        }

        $criteria = $parameters->get('criteria');

        foreach ($criteria as $name => $data) {
            if (!$grid->hasFilter($name)) {
                continue;
            }

            $filter = $grid->getFilter($name);

            $this->filtersRegistry->get($filter->getType())->apply($dataSource, $name, $data, $filter->getOptions());
        }
    }
}
