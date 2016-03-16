<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentMethodTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PaymentMethodTranslation', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodTranslationType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_payment_method_translation');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', 'text', Argument::type('array'))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('description', 'textarea', Argument::type('array'))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => 'PaymentMethodTranslation',
                    'validation_groups' => ['sylius'],
                ]
            )
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
