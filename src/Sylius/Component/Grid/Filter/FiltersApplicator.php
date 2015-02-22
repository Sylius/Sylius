<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\DataSource\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FiltersApplicator implements FiltersApplicatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $filterRegistry;

    /**
     * @param ServiceRegistryInterface $filterRegistry
     */
    public function __construct(ServiceRegistryInterface $filterRegistry)
    {
        $this->filterRegistry = $filterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Grid $grid, DataSourceInterface $dataSource, Parameters $parameters)
    {
        $filters = $parameters->get('filters', array());

        foreach ($filters as $name => $data) {
            if (!$grid->hasFilter($name)) {
                continue;
            }

            /** @var Filter $filterDefinition */
            $filterDefinition = $grid->getFilter($name);

            /** @var FilterInterface $filter */
            $filter = $this->filterRegistry->get($filterDefinition->getType());

            $optionsResolver = new OptionsResolver();
            $filter->setOptions($optionsResolver);

            $options = $optionsResolver->resolve($filterDefinition->getOptions());

            $filter->apply($dataSource, $name, $data, $options);
        }
    }
}
