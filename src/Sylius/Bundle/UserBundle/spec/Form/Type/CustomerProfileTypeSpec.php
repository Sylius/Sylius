<?php

namespace spec\Sylius\Bundle\UserBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerProfileTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Customer', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('email', 'email', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('birthday', 'birthday', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('gender', 'sylius_gender')->shouldbeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_customer_profile');
    }
}
