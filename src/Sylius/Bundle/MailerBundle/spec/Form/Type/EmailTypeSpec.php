<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MailerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EmailTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Email', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Form\Type\EmailType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('code', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('senderName', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('senderAddress', 'email', argument::any())
            ->willreturn($builder)
        ;

        $builder
            ->add('content', 'textarea', argument::any())
            ->willreturn($builder)
        ;

        $builder
            ->add('subject', 'text', argument::any())
            ->willreturn($builder)
        ;

        $builder
            ->add('template', 'sylius_email_template_choice', argument::any())
            ->willreturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class_and_validation_groups(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Email',
                'validation_groups' => array('sylius')
            ))
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_email');
    }
}
