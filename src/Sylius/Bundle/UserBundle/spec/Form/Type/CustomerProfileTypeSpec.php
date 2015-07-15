<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerProfileTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Customer', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType');
    }

    public function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('email', 'email', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('birthday', 'birthday', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('gender', 'sylius_gender')->shouldbeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_customer_profile');
    }
}
