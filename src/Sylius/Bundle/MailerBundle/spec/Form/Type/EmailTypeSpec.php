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
use Sylius\Bundle\MailerBundle\Form\Type\EmailType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class EmailTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Email', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EmailType::class);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
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
            ->add('senderAddress', 'email', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('content', 'textarea', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('subject', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('template', 'sylius_email_template_choice', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class_and_validation_groups(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Email',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_email');
    }
}
