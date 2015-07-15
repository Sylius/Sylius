<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Context\CurrencyContext;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class FixedFeeCalculatorConfigurationTypeSpec extends ObjectBehavior
{
    function let(CurrencyContext $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\Calculator\FixedFeeCalculatorConfigurationType');
    }

    function it_is_abstract_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form($currencyContext, FormBuilderInterface $builder)
    {
        $currencyContext->getCurrency()->willReturn('USD')->shouldBeCalled();

        $builder
            ->add('amount', 'sylius_money', array(
                'label'    => 'sylius.form.payment_method.fee_calculator.fixed.amount',
                'currency' => 'USD',
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, array());
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_fee_calculator_fixed');
    }
}