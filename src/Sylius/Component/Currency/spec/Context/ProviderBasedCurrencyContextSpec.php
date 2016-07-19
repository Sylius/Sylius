<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\ProviderBasedCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @mixin ProviderBasedCurrencyContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProviderBasedCurrencyContextSpec extends ObjectBehavior
{
    function let(CurrencyProviderInterface $currencyProvider)
    {
        $this->beConstructedWith($currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Context\ProviderBasedCurrencyContext');
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_the_channels_default_currency(
        CurrencyProviderInterface $currencyProvider,
        CurrencyInterface $currency
    ) {
        $currencyProvider->getDefaultCurrencyCode()->willReturn($currency);

        $this->getCurrencyCode()->shouldReturn($currency);
    }
}
