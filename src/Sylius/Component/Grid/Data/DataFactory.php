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

use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\DataSource\DataSourceProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filter\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorter\SorterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataFactory implements DataFactoryInterface
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
    public function createData(Grid $grid, Parameters $parameters)
    {
        $dataSource = $this->dataSourceProvider->getDataSource($grid);

        $this->filtersApplicator->apply($grid, $dataSource, $parameters);
        $this->sorter->sort($grid, $dataSource, $parameters);

        $data = $dataSource->getData();

        if ($data instanceof Pagerfanta) {
            $data->setCurrentPage($parameters->get('page', 1));
        }

        return $data;
    }
}
