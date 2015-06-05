<?php

namespace spec\Sylius\Bundle\PricingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class VolumePriceConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\Type\VolumePriceConfigurationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('min', 'number', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('max', 'number', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('price', 'sylius_money', Argument::type('array'))->shouldBeCalled();

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_price_calculator_volume_based_configuration');
    }
}
