<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProvider;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencyProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyProvider::class);
    }

    function it_is_a_currency_provider_interface()
    {
        $this->shouldImplement(CurrencyProviderInterface::class);
    }

    function it_returns_all_enabled_currencies(RepositoryInterface $currencyRepository, CurrencyInterface $currency)
    {
        $currencyRepository->findBy(['enabled' => true])->willReturn([$currency]);
        $currency->getCode()->willReturn('PLN');

        $this->getAvailableCurrenciesCodes()->shouldReturn(['PLN']);
    }

    function it_returns_the_default_currency()
    {
        $this->getDefaultCurrencyCode()->shouldReturn('EUR');
    }
}
