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
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Filtering\FiltersApplicator;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @mixin FiltersApplicator
 *
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

    function it_filters_data_source_based_on_criteria_parameter(
        ServiceRegistryInterface $filtersRegistry,
        FilterInterface $stringFilter,
        Grid $grid,
        Filter $filter,
        DataSourceInterface $dataSource
    ) {
        $parameters = new Parameters(['criteria' => ['keywords' => 'Banana', 'enabled' => true]]);

        $grid->hasFilter('keywords')->willReturn(true);
        $grid->hasFilter('enabled')->willReturn(false);

        $grid->getFilter('keywords')->willReturn($filter);
        $filter->getType()->willReturn('string');
        $filter->getOptions()->willReturn(['fields' => ['firstName', 'lastName']]);

        $filtersRegistry->get('string')->willReturn($stringFilter);

        $stringFilter->apply($dataSource, 'keywords', 'Banana', ['fields' => ['firstName', 'lastName']])->shouldBeCalled();

        $this->apply($dataSource, $grid, $parameters);
    }
}
