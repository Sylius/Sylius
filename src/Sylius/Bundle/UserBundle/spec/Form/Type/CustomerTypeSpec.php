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

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Sylius\Component\User\Model\Customer', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\CustomerType');
    }

    function it_extends_abstract_resource_type()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_customer');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('email', 'email', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('birthday', 'birthday', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('gender', 'sylius_gender', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('groups', 'sylius_group_choice', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('user', 'sylius_user', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }
}
