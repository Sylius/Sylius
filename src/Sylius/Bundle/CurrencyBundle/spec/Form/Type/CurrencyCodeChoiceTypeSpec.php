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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class CurrencyCodeChoiceTypeSpec extends ObjectBehavior
{
    function let(CurrencyProviderInterface $currencyProvider)
    {
        $this->beConstructedWith($currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyCodeChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_define_assigned_data_class_and_validation_groups(
        $currencyProvider,
        OptionsResolver $resolver,
        Currency $currency
    ) {
        $currencyProvider->getAvailableCurrencies()->shouldBeCalled()->willReturn([$currency]);
        $currency->getCode()->shouldBeCalled()->willReturn('EUR');
        $currency->getName()->shouldBeCalled()->willReturn('Euro');

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choices' => ['EUR' => 'EUR - Euro'],
            ])
            ->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_currency_code_choice');
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
