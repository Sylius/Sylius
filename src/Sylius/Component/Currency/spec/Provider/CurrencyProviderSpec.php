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
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Provider\CurrencyProvider');
    }

    function it_implements_Sylius_currency_provider_interface()
    {
        $this->shouldImplement(CurrencyProviderInterface::class);
    }

    function it_returns_all_enabled_currencies(CurrencyInterface $currency, $currencyRepository)
    {
        $currencyRepository->findBy(['enabled' => true])->shouldBeCalled()->willReturn([$currency]);

        $this->getAvailableCurrencies()->shouldReturn([$currency]);
    }
}
