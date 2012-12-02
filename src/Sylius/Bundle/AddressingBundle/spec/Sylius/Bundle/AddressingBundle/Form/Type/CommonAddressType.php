<?php

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

class CommonAddressType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Address');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\CommonAddressType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_common_address_fields($builder)
    {
        $builder->add('firstname', 'text', ANY_ARGUMENT)->shouldBeCalled()->willReturn($builder);
        $builder->add('lastname', 'text', ANY_ARGUMENT)->shouldBeCalled()->willReturn($builder);
        $builder->add('street', 'text', ANY_ARGUMENT)->shouldBeCalled()->willReturn($builder);
        $builder->add('city', 'text', ANY_ARGUMENT)->shouldBeCalled()->willReturn($builder);
        $builder->add('postcode', 'text', ANY_ARGUMENT)->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }
}
