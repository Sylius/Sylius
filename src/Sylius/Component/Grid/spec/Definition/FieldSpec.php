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

use Sylius\Component\Grid\Definition\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Field
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FieldSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromNameAndType', ['enabled', 'boolean']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Definition\Field');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('enabled');
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('boolean');
    }
    
    function it_has_path_which_defaults_to_name()
    {
        $this->getPath()->shouldReturn('enabled');
        
        $this->setPath('method.enabled');
        $this->getPath()->shouldReturn('method.enabled');
    }
    
    function it_has_label_which_defaults_to_name()
    {
        $this->getLabel()->shouldReturn('enabled');
        
        $this->setLabel('Is enabled?');
        $this->getLabel()->shouldReturn('Is enabled?');
    }

    function it_is_sortable_by_default()
    {
        $this->shouldBeSortable();
        $this->getSortingPath()->shouldReturn('enabled');
    }

    function it_can_be_not_sortable()
    {
        $this->setSortable(false);
        $this->shouldNotBeSortable();
    }
    
    function its_sorting_path_is_mutable()
    {
        $this->setSortingPath('method.enabled');
        $this->getSortingPath()->shouldReturn('method.enabled');
    }
    
    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn([]);
    }

    function it_can_have_options()
    {
        $this->setOptions(['template' => 'SyliusUiBundle:Grid/Field:_status.html.twig']);
        $this->getOptions()->shouldReturn(['template' => 'SyliusUiBundle:Grid/Field:_status.html.twig']);
    }
}
