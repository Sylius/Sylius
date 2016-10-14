<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Data;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataProvider;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\SorterInterface;

/**
 * @mixin DataProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DataProviderSpec extends ObjectBehavior
{
    function let(DataSourceProviderInterface $dataSourceProvider, FiltersApplicatorInterface $filtersApplicator, SorterInterface $sorter)
    {
        $this->beConstructedWith($dataSourceProvider, $filtersApplicator, $sorter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DataProvider::class);
    }

    function it_implements_grid_data_provider_interface()
    {
        $this->shouldImplement(DataProviderInterface::class);
    }

    function it_gets_data_from_the_data_source(
        DataSourceProviderInterface $dataSourceProvider,
        DataSourceInterface $dataSource,
        FiltersApplicatorInterface $filtersApplicator,
        SorterInterface $sorter,
        Grid $grid,
        Parameters $parameters
    ) {
        $dataSourceProvider->getDataSource($grid, $parameters)->willReturn($dataSource);

        $filtersApplicator->apply($dataSource, $grid, $parameters)->shouldBeCalled();
        $sorter->sort($dataSource, $grid, $parameters)->shouldBeCalled();

        $dataSource->getData($parameters)->willReturn(['foo', 'bar']);

        $this->getData($grid, $parameters)->shouldReturn(['foo', 'bar']);
    }
}
