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
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class FiltersCriteriaResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FiltersCriteriaResolver::class);
    }

    function it_implements_filters_criteria_resolver_interface()
    {
        $this->shouldImplement(FiltersCriteriaResolverInterface::class);
    }

    function it_checks_whether_any_criteria_are_available(Grid $grid, Filter $filter)
    {
        $emptyParameters = new Parameters();
        $criteriaParameters = new Parameters(['criteria' => ['czapla']]);

        $grid->getFilters()->willReturn([]);

        $this->hasCriteria($grid, $emptyParameters)->shouldReturn(false);

        $grid->getFilters()->willReturn([]);

        $this->hasCriteria($grid, $criteriaParameters)->shouldReturn(true);

        $grid->getFilters()->willReturn([$filter]);

        $this->hasCriteria($grid, $emptyParameters)->shouldReturn(false);

        $grid->getFilters()->willReturn([$filter]);

        $this->hasCriteria($grid, $criteriaParameters)->shouldReturn(true);

        $grid->getFilters()->willReturn([$filter]);
        $filter->getCriteria()->willReturn('czapla');

        $this->hasCriteria($grid, $emptyParameters)->shouldReturn(true);

        $grid->getFilters()->willReturn([$filter]);
        $filter->getCriteria()->willReturn('czapla');

        $this->hasCriteria($grid, $criteriaParameters)->shouldReturn(true);
    }

    function it_gets_default_criteria_from_grid_filters(Grid $grid, Filter $firstFilter, Filter $secondFilter)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();

        $firstFilter->getCriteria()->willReturn('Pug');
        $secondFilter->getCriteria()->willReturn(['start' => $startDate, 'end' => $endDate]);

        $grid->getFilters()->willReturn(['favourite' => $firstFilter, 'date' => $secondFilter]);

        $this->getCriteria($grid, new Parameters())->shouldBeSameAs([
            'favourite' => 'Pug',
            'date' => ['start' => $startDate, 'end' => $endDate],
        ]);
    }

    function it_gets_criteria_from_parameters(Grid $grid, Filter $firstFilter, Filter $secondFilter)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();

        $firstFilter->getCriteria()->willReturn(null);
        $secondFilter->getCriteria()->willReturn(null);

        $grid->getFilters()->willReturn(['favourite' => $firstFilter, 'date' => $secondFilter]);

        $parameters = new Parameters([
            'criteria' => [
                'favourite' => 'Pug',
                'date' => ['start' => $startDate, 'end' => $endDate],
            ]
        ]);

        $this->getCriteria($grid, $parameters)->shouldBeSameAs([
            'favourite' => 'Pug',
            'date' => ['start' => $startDate, 'end' => $endDate],
        ]);
    }

    function it_prioritizes_parameters_criteria_over_filters_default(
        Grid $grid,
        Filter $firstFilter,
        Filter $secondFilter
    ) {
        $parametersDate = new \DateTime();

        $firstFilter->getCriteria()->willReturn('Rum');
        $secondFilter->getCriteria()->willReturn(null);

        $grid->getFilters()->willReturn(['favourite' => $firstFilter, 'date' => $secondFilter]);

        $parameters = new Parameters([
            'criteria' => [
                'favourite' => 'Pug',
                'date' => ['now' => $parametersDate],
            ]
        ]);

        $this->getCriteria($grid, $parameters)->shouldBeSameAs([
            'favourite' => 'Pug',
            'date' => ['now' => $parametersDate],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!is_array($subject) || !is_array($key)) {
                    return false;
                }

                if (count($subject) !== count($key)) {
                    return false;
                }

                if (0 !== count(array_diff_key($key, $subject))) {
                    return false;
                }

                foreach (array_keys($key) as $arrayKey) {
                    if ($key[$arrayKey] !== $subject[$arrayKey]) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
