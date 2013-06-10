<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Payment method form type spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PaymentMethodType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PaymentMethod', array('sylius'));
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_builds_form_with_proper_fields($builder)
    {
        $builder
            ->add('name', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'textarea', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('gateway', 'sylius_payment_gateway_choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'PaymentMethod',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }
}
