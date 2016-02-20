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

use Sylius\Component\Grid\Definition\Action;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Action
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ActionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromNameAndType', array('view', 'link'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Definition\Action');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('view');
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('link');
    }

    function it_has_label_which_defaults_to_name()
    {
        $this->getLabel()->shouldReturn('view');
        
        $this->setLabel('Read book');
        $this->getLabel()->shouldReturn('Read book');
    }

    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn(array());
    }

    function it_can_have_options()
    {
        $this->setOptions(array('route' => 'sylius_admin_product_update'));
        $this->getOptions()->shouldReturn(array('route' => 'sylius_admin_product_update'));
    }
}
