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
use Prophecy\Argument;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;

/**
 * @mixin Grid
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromCodeAndDriverConfiguration', array('sylius_admin_tax_category', 'doctrine/orm', array(
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code']
        )));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Definition\Grid');
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
        $this->getDriverConfiguration()->shouldReturn(array(
            'resource' => 'sylius.tax_category',
            'method' => 'createByCodeQueryBuilder',
            'arguments' => ['$code']
        ));
    }
    
    function it_has_empty_sorting_configuration_by_default()
    {
        $this->getSorting()->shouldReturn(array());
    }
    
    function it_can_have_sorting_configuration()
    {
        $this->setSorting(array('name' => 'desc'));
        $this->getSorting()->shouldReturn(array('name' => 'desc'));
    }
    
    function it_does_not_have_any_fields_by_default()
    {
        $this->getFields()->shouldReturn(array());
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
            ->during('addField', array($secondField))
        ;
    }
    
    function it_knows_if_field_with_given_name_already_exists(Field $field)
    {
        $field->getName()->willReturn('enabled');
        $this->addField($field);
        
        $this->hasField('enabled')->shouldReturn(true);
        $this->hasField('parent')->shouldReturn(false);
    }

    function it_does_not_have_any_action_groups_by_default()
    {
        $this->getActionGroups()->shouldReturn(array());
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
            ->during('addActionGroup', array($secondActionGroup))
        ;
    }

    function it_knows_if_action_group_with_given_name_already_exists(ActionGroup $actionGroup)
    {
        $actionGroup->getName()->willReturn('row');
        $this->addActionGroup($actionGroup);

        $this->hasActionGroup('row')->shouldReturn(true);
        $this->hasActionGroup('default')->shouldReturn(false);
    }

    function it_does_not_have_any_filters_by_default()
    {
        $this->getFilters()->shouldReturn(array());
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
            ->during('addFilter', array($secondFilter))
        ;
    }

    function it_knows_if_filter_with_given_name_already_exists(Filter $filter)
    {
        $filter->getName()->willReturn('enabled');
        $this->addFilter($filter);

        $this->hasFilter('enabled')->shouldReturn(true);
        $this->hasFilter('created_at')->shouldReturn(false);
    }
}
