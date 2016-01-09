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

use Sylius\Component\Grid\Definition\Filter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Filter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromNameAndType', array('keywords', 'string'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Definition\Filter');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('keywords');
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('string');
    }

    function it_has_label_which_defaults_to_name()
    {
        $this->getLabel()->shouldReturn('keywords');
        
        $this->setLabel('Search by keyword');
        $this->getLabel()->shouldReturn('Search by keyword');
    }
    
    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn(array());
    }

    function it_can_have_options()
    {
        $this->setOptions(array('fields' => ['firstName', 'lastName', 'email']));
        $this->getOptions()->shouldReturn(array('fields' => ['firstName', 'lastName', 'email']));
    }
}
