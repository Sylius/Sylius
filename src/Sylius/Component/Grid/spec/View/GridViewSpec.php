<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GridView
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridViewSpec extends ObjectBehavior
{
    function let(Grid $gridDefinition, Parameters $parameters)
    {
        $this->beConstructedWith(array('foo', 'bar'), $gridDefinition, $parameters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\View\GridView');
    }

    function it_has_data()
    {
        $this->getData()->shouldReturn(array('foo', 'bar'));
    }

    function it_has_definition(Grid $gridDefinition)
    {
        $this->getDefinition()->shouldReturn($gridDefinition);
    }

    function it_has_parameters(Parameters $parameters)
    {
        $this->getParameters()->shouldReturn($parameters);
    }
}
