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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyProviderSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Provider\CurrencyProvider');
    }

    public function it_implements_Sylius_currency_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Currency\Provider\CurrencyProviderInterface');
    }

    public function it_returns_all_enabled_currencies(CurrencyInterface $currency, $currencyRepository)
    {
        $currencyRepository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn(array($currency));

        $this->getAvailableCurrencies()->shouldReturn(array($currency));
    }
}
