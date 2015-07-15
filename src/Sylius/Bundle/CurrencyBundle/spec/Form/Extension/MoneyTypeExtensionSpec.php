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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class MoneyTypeExtensionSpec extends ObjectBehavior
{
    public function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Form\Extension\MoneyTypeExtension');
    }

    public function it_has_options($currencyContext, OptionsResolverInterface $resolver)
    {
        $currencyContext->getCurrency()->shouldBeCalled()->willReturn('EUR');
        $resolver->setDefaults(array('currency' => 'EUR'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_extends_a_form_type()
    {
        $this->getExtendedType()->shouldReturn('sylius_money');
    }
}
