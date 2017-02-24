<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class GridSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromCodeAndDriverConfiguration', ['sylius_admin_tax_category', 'doctrine/orm', [
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code']
        ]]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Grid::class);
    }

    function it_has_code()
    {
        $this->getCode()->shouldReturn('sylius_admin_tax_category');
    }

    function it_has_driver()
    {
        $this->getDriver()->shouldReturn('doctrine/orm');
    }

    function it_has_driver_configuration()
    {
        $this->getDriverConfiguration()->shouldReturn([
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code']
        ]);
    }

    function its_driver_configuration_is_mutable()
    {
        $this->setDriverConfiguration(['foo' => 'bar']);
        $this->getDriverConfiguration()->shouldReturn(['foo' => 'bar']);
    }

    function it_has_empty_sorting_configuration_by_default()
    {
        $this->getSorting()->shouldReturn([]);
    }

    function it_can_have_sorting_configuration()
    {
        $this->setSorting(['name' => 'asc']);
        $this->getSorting()->shouldReturn(['name' => 'asc']);
    }

    function it_has_no_pagination_limits_by_default()
    {
        $this->getLimits()->shouldReturn([]);
    }

    function its_pagination_limits_can_be_configured()
    {
        $this->setLimits([20, 50, 100]);
        $this->getLimits()->shouldReturn([20, 50, 100]);
    }

    function it_does_not_have_any_fields_by_default()
    {
        $this->getFields()->shouldReturn([]);
    }

    function it_can_have_field_definitions(Field $field)
    {
        $field->getName()->willReturn('description');

        $this->addField($field);
        $this->getField('description')->shouldReturn($field);
    }

    function it_cannot_have_two_fields_with_the_same_name(Field $firstField, Field $secondField)
    {
        $firstField->getName()->willReturn('created_at');
        $secondField->getName()->willReturn('created_at');

        $this->addField($firstField);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addField', [$secondField])
        ;
    }

    function it_knows_if_field_with_given_name_already_exists(Field $field)
    {
        $field->getName()->willReturn('enabled');
        $this->addField($field);

        $this->hasField('enabled')->shouldReturn(true);
        $this->hasField('parent')->shouldReturn(false);
    }

    function it_can_remove_field(Field $field)
    {
        $field->getName()->willReturn('enabled');
        $this->addField($field);

        $this->removeField('enabled');
        $this->hasField('enabled')->shouldReturn(false);
    }

    function it_can_replace_field(Field $firstField, Field $secondField)
    {
        $firstField->getName()->willReturn('enabled');
        $secondField->getName()->willReturn('enabled');
        $this->addField($firstField);

        $this->setField($secondField);
        $this->getField('enabled')->shouldReturn($secondField);
    }

    function it_does_not_have_any_action_groups_by_default()
    {
        $this->getActionGroups()->shouldReturn([]);
    }

    function it_can_have_action_group_definitions(ActionGroup $actionGroup)
    {
        $actionGroup->getName()->willReturn('default');

        $this->addActionGroup($actionGroup);
        $this->getActionGroup('default')->shouldReturn($actionGroup);
    }

    function it_cannot_have_two_action_groups_with_the_same_name(ActionGroup $firstActionGroup, ActionGroup $secondActionGroup)
    {
        $firstActionGroup->getName()->willReturn('row');
        $secondActionGroup->getName()->willReturn('row');

        $this->addActionGroup($firstActionGroup);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addActionGroup', [$secondActionGroup])
        ;
    }

    function it_knows_if_action_group_with_given_name_already_exists(ActionGroup $actionGroup)
    {
        $actionGroup->getName()->willReturn('row');
        $this->addActionGroup($actionGroup);

        $this->hasActionGroup('row')->shouldReturn(true);
        $this->hasActionGroup('default')->shouldReturn(false);
    }

    function it_can_remove_action_group(ActionGroup $actionGroup)
    {
        $actionGroup->getName()->willReturn('row');
        $this->addActionGroup($actionGroup);

        $this->removeActionGroup('row');
        $this->hasActionGroup('row')->shouldReturn(false);
    }

    function it_can_replace_action_group(ActionGroup $firstActionGroup, ActionGroup $secondActionGroup)
    {
        $firstActionGroup->getName()->willReturn('row');
        $secondActionGroup->getName()->willReturn('row');
        $this->addActionGroup($firstActionGroup);

        $this->setActionGroup($secondActionGroup);
        $this->getActionGroup('row')->shouldReturn($secondActionGroup);
    }

    function it_returns_actions_for_given_group(ActionGroup $actionGroup, Action $action)
    {
        $actionGroup->getName()->willReturn('row');
        $actionGroup->getActions()->willReturn([$action]);
        $this->addActionGroup($actionGroup);

        $this->getActions('row')->shouldReturn([$action]);
    }

    function it_does_not_have_any_filters_by_default()
    {
        $this->getFilters()->shouldReturn([]);
    }

    function it_can_have_filter_definitions(Filter $filter)
    {
        $filter->getName()->willReturn('enabled');

        $this->addFilter($filter);
        $this->getFilter('enabled')->shouldReturn($filter);
    }

    function it_cannot_have_two_filters_with_the_same_name(Filter $firstFilter, Filter $secondFilter)
    {
        $firstFilter->getName()->willReturn('created_at');
        $secondFilter->getName()->willReturn('created_at');

        $this->addFilter($firstFilter);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addFilter', [$secondFilter])
        ;
    }

    function it_knows_if_filter_with_given_name_already_exists(Filter $filter)
    {
        $filter->getName()->willReturn('enabled');
        $this->addFilter($filter);

        $this->hasFilter('enabled')->shouldReturn(true);
        $this->hasFilter('created_at')->shouldReturn(false);
    }

    function it_can_remove_filter(Filter $filter)
    {
        $filter->getName()->willReturn('enabled');
        $this->addFilter($filter);

        $this->removeFilter('enabled');
        $this->hasFilter('enabled')->shouldReturn(false);
    }

    function it_can_replace_filter(Filter $firstFilter, Filter $secondFilter)
    {
        $firstFilter->getName()->willReturn('enabled');
        $secondFilter->getName()->willReturn('enabled');
        $this->addFilter($firstFilter);

        $this->setFilter($secondFilter);
        $this->getFilter('enabled')->shouldReturn($secondFilter);
    }
}
