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

namespace spec\Sylius\Component\Grid\Filtering;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class FiltersApplicatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $filtersRegistry, FiltersCriteriaResolverInterface $criteriaResolver): void
    {
        $this->beConstructedWith($filtersRegistry, $criteriaResolver);
    }

    function it_implements_filters_applicator_interface(): void
    {
        $this->shouldImplement(FiltersApplicatorInterface::class);
    }

    function it_does_nothing_when_there_are_no_filtering_criteria(
        FiltersCriteriaResolverInterface $criteriaResolver,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ): void {
        $parameters = new Parameters();

        $grid->getFilters()->willReturn(['keywords' => $filter]);

        $criteriaResolver->hasCriteria($grid, $parameters)->willReturn(false);

        $stringFilter->apply($dataSource, Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->apply($dataSource, $grid, $parameters);
    }

    function it_filters_data_source_based_on_filters_default_criteria(
        ServiceRegistryInterface $filtersRegistry,
        FiltersCriteriaResolverInterface $criteriaResolver,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ): void {
        $parameters = new Parameters();

        $grid->getFilters()->willReturn(['keywords' => $filter]);

        $grid->hasFilter('keywords')->willReturn(true);
        $grid->getFilter('keywords')->willReturn($filter);

        $filter->getType()->willReturn('string');
        $filter->getOptions()->willReturn(['fields' => ['firstName', 'lastName']]);

        $criteriaResolver->hasCriteria($grid, $parameters)->willReturn(true);
        $criteriaResolver->getCriteria($grid, $parameters)->willReturn(['keywords' => 'Banana']);

        $filtersRegistry->get('string')->willReturn($stringFilter);

        $stringFilter->apply($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']])->shouldBeCalled();

        $this->apply($dataSource, $grid, new Parameters());
    }

    function it_filters_data_source_based_on_criteria_parameter(
        ServiceRegistryInterface $filtersRegistry,
        FiltersCriteriaResolverInterface $criteriaResolver,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ): void {
        $parameters = new Parameters(['criteria' => ['keywords' => 'Banana', 'enabled' => true]]);

        $grid->getFilters()->willReturn(['keywords' => $filter]);

        $grid->hasFilter('keywords')->willReturn(true);
        $grid->hasFilter('enabled')->willReturn(false);

        $grid->getFilter('keywords')->willReturn($filter);
        $filter->getType()->willReturn('string');
        $filter->getOptions()->willReturn(['fields' => ['firstName', 'lastName']]);

        $criteriaResolver->hasCriteria($grid, $parameters)->willReturn(true);
        $criteriaResolver->getCriteria($grid, $parameters)->willReturn(['keywords' => 'Banana', 'enabled' => true]);

        $filtersRegistry->get('string')->willReturn($stringFilter);

        $stringFilter->apply($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']])->shouldBeCalled();

        $this->apply($dataSource, $grid, $parameters);
    }
}
