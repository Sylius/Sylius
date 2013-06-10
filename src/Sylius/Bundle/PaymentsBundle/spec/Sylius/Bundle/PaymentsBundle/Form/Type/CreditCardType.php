<?php

/*
 * This file is an addition to the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Credit Card form type spec.
 *
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
class CreditCardType extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('CreditCard', array('sylius'));
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    public function it_builds_form_with_proper_fields($builder)
    {
        $builder
            ->add('type', 'choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('cardholderName', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('number', 'number', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('securityCode', 'number', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('expiryMonth', 'choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('expiryYear', 'choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'CreditCard',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }
}
