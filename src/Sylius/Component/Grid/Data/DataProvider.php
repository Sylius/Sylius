<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\SorterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @var DataSourceProviderInterface
     */
    private $dataSourceProvider;

    /**
     * @var FiltersApplicatorInterface
     */
    private $filtersApplicator;

    /**
     * @var SorterInterface
     */
    private $sorter;

    /**
     * @param DataSourceProviderInterface $dataSourceProvider
     * @param FiltersApplicatorInterface $filtersApplicator
     * @param SorterInterface $sorter
     */
    public function __construct(DataSourceProviderInterface $dataSourceProvider, FiltersApplicatorInterface $filtersApplicator, SorterInterface $sorter)
    {
        $this->dataSourceProvider = $dataSourceProvider;
        $this->filtersApplicator = $filtersApplicator;
        $this->sorter = $sorter;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(Grid $grid, Parameters $parameters)
    {
        $dataSource = $this->dataSourceProvider->getDataSource($grid, $parameters);

        $this->filtersApplicator->apply($dataSource, $grid, $parameters);
        $this->sorter->sort($dataSource, $grid, $parameters);

        return $dataSource->getData($parameters);
    }
}
