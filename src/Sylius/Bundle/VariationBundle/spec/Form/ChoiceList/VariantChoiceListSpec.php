<?php

namespace spec\Sylius\Bundle\VariationBundle\Form\ChoiceList;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

class VariantChoiceListSpec extends ObjectBehavior
{
    function let(VariableInterface $variable, VariantInterface $variant)
    {
        $variable->getVariants()->shouldBeCalled()->willReturn(array($variant));

        $this->beConstructedWith($variable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\ChoiceList\VariantChoiceList');
    }
}
