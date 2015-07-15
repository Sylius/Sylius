<?php

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InStockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock');
    }

    function it_is_a_contraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_has_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_in_stock');
    }

    function it_has_a_target()
    {
        $this->getTargets()->shouldReturn('class');
    }
}
