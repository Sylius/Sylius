<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ContactBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class RequestTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Contact', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContactBundle\Form\Type\RequestType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('firstName', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('lastName', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('email', 'email', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('message', 'textarea', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('topic', 'sylius_contact_topic_choice', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_should_define_assigned_data_class_and_validation_groups(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Contact',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_contact_request');
    }
}
