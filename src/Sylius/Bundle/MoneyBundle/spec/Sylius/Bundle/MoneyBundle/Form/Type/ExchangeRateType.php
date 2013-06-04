<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius exchange rate form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExchangeRateType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ExchangeRate');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Form\Type\ExchangeRateType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_proper_fields($builder)
    {
        $builder
            ->add('currency', 'currency', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('rate', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'ExchangeRate'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
