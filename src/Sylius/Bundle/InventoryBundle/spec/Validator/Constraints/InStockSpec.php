<?php

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;

class InStockSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock');
    }

    public function it_is_a_contraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    public function it_has_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_in_stock');
    }

    public function it_has_a_target()
    {
        $this->getTargets()->shouldReturn('class');
    }
}
