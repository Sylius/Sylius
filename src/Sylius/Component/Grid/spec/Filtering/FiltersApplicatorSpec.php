<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Filtering;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FiltersApplicatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $filtersRegistry)
    {
        $this->beConstructedWith($filtersRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FiltersApplicator::class);
    }

    function it_implements_filters_applicator_interface()
    {
        $this->shouldImplement(FiltersApplicatorInterface::class);
    }

    function it_does_nothing_when_there_are_no_filtering_criteria(
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ) {
        $parameters = new Parameters();

        $grid->getFilters()->willReturn([$filter]);

        $filter->getCriteria()->willReturn(null);

        $stringFilter->apply(
            $dataSource,
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $this->apply($dataSource, $grid, $parameters);
    }

    function it_filters_data_source_based_on_filters_default_criteria(
        ServiceRegistryInterface $filtersRegistry,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ) {
        $grid->getFilters()->willReturn(['keywords' => $filter]);

        $grid->hasFilter('keywords')->willReturn(true);
        $grid->getFilter('keywords')->willReturn($filter);

        $filter->getType()->willReturn('string');
        $filter->getOptions()->willReturn(['fields' => ['firstName', 'lastName']]);
        $filter->getCriteria()->willReturn('Banana');

        $filtersRegistry->get('string')->willReturn($stringFilter);

        $stringFilter->apply($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']])->shouldBeCalled();

        $this->apply($dataSource, $grid, new Parameters());
    }

    function it_filters_data_source_based_on_criteria_parameter(
        ServiceRegistryInterface $filtersRegistry,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ) {
        $parameters = new Parameters(['criteria' => ['keywords' => 'Banana', 'enabled' => true]]);

        $grid->getFilters()->willReturn(['keywords' => $filter]);

        $grid->hasFilter('keywords')->willReturn(true);
        $grid->hasFilter('enabled')->willReturn(false);

        $grid->getFilter('keywords')->willReturn($filter);
        $filter->getType()->willReturn('string');
        $filter->getOptions()->willReturn(['fields' => ['firstName', 'lastName']]);
        $filter->getCriteria()->willReturn(null);

        $filtersRegistry->get('string')->willReturn($stringFilter);

        $stringFilter->apply($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']])->shouldBeCalled();

        $this->apply($dataSource, $grid, $parameters);
    }
}
