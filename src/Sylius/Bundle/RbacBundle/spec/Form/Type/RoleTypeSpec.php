<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RoleTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Role', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Form\Type\RoleType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'textarea', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('parent', 'sylius_role_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('securityRoles', 'sylius_security_role_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('permissions', 'sylius_permission_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::type('closure'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class_and_validation_groups(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Role',
                'validation_groups' => array('sylius')
            ))
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_role');
    }
}
