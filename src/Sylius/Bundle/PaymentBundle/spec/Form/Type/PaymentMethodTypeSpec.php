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
use Sylius\Component\Payment\Calculator\FeeCalculatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethodTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $feeCalculatorRegistry)
    {
        $this->beConstructedWith('PaymentMethod', array('sylius'), $feeCalculatorRegistry);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_proper_fields(
        $feeCalculatorRegistry,
        FeeCalculatorInterface $feeCalculatorTest,
        Form $form,
        FormBuilder $builder,
        FormFactoryInterface $formFactory
    ) {
        $builder->getFormFactory()->willReturn($formFactory)->shouldBeCalled();

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

        $builder
            ->add('enabled', 'checkbox', Argument::type('array'))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('gateway', 'sylius_payment_gateway_choice', Argument::type('array'))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('feeCalculator', 'sylius_fee_calculator_choice', Argument::type('array'))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\PaymentBundle\Form\Type\EventListener\BuildPaymentMethodFeeCalculatorFormSubscriber'))
            ->shouldBeCalled()
        ;

        $feeCalculatorRegistry->all()->willReturn(array('test' => $feeCalculatorTest))->shouldBeCalled();

        $feeCalculatorTest->getType()->willReturn('test')->shouldBeCalled();
        $builder->create('feeCalculatorConfiguration', 'sylius_fee_calculator_test')->willReturn($builder)->shouldBeCalled();
        $builder->getForm()->willReturn($form)->shouldBeCalled();

        $builder->setAttribute('feeCalculatorsConfigurations', array('test' => $form))->willReturn($builder)->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
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

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_payment_method');
    }
}
