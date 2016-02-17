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
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class TopicTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ContactTranslation', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContactBundle\Form\Type\TopicTranslationType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('title', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => 'ContactTranslation',
                    'validation_groups' => ['sylius'],
                ]
            )
            ->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_contact_topic_translation');
    }
}
