<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class CurrencyCodeChoiceTypeSpec extends ObjectBehavior
{
    public function let(CurrencyProviderInterface $currencyProvider)
    {
        $this->beConstructedWith($currencyProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyCodeChoiceType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_should_define_assigned_data_class_and_validation_groups(
        $currencyProvider,
        OptionsResolverInterface $resolver,
        Currency $currency
    ) {
        $currencyProvider->getAvailableCurrencies()->shouldBeCalled()->willReturn(array($currency));
        $currency->getCode()->shouldBeCalled()->willReturn('EUR');
        $currency->getName()->shouldBeCalled()->willReturn('Euro');

        $resolver
            ->setDefaults(array(
                'choices' => array('EUR' => 'EUR - Euro'),
            ))
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_currency_code_choice');
    }

    public function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
