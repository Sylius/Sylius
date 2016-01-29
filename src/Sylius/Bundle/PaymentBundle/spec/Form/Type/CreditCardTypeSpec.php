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

/**
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
class CreditCardTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('CreditCard', ['sylius']);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('type', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('cardholderName', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('number', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('securityCode', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('expiryMonth', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('expiryYear', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class_and_validation_groups(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'CreditCard',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_credit_card');
    }
}
