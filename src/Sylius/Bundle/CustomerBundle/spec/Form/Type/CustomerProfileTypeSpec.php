<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CustomerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerProfileTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Customer', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('email', 'email', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('birthday', 'birthday', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('gender', 'sylius_gender', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('phoneNumber', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_customer_profile');
    }
}
