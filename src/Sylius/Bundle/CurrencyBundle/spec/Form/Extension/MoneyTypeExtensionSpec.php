<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Form\Extension;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class MoneyTypeExtensionSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Form\Extension\MoneyTypeExtension');
    }

    function it_has_options($currencyContext, OptionsResolver $resolver)
    {
        $currencyContext->getCurrency()->shouldBeCalled()->willReturn('EUR');
        $resolver->setDefaults(['currency' => 'EUR'])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_extends_a_form_type()
    {
        $this->getExtendedType()->shouldReturn('sylius_money');
    }
}
