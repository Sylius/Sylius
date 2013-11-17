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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExchangeRateTypeSpec extends ObjectBehavior
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

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('currency', 'currency', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('rate', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'ExchangeRate'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
