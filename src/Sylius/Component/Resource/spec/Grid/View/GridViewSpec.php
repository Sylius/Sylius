<?php

namespace spec\Sylius\Component\Resource\Grid\View;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GridViewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Grid\GridView');
    }
}
