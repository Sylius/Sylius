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
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\ImmutableCurrencyContext;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin ImmutableCurrencyContext
 */
class ImmutableCurrencyContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Context\ImmutableCurrencyContext');
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_gets_currency_from_the_repository(
        RepositoryInterface $currencyRepository,
        CurrencyInterface $currency
    ) {
        $currencyRepository->findOneBy(['code' => 'EUR'])->willReturn($currency);

        $this->getCurrency()->shouldReturn($currency);
    }

    function it_gets_null_if_currency_cannot_be_found(RepositoryInterface $currencyRepository)
    {
        $currencyRepository->findOneBy(['code' => 'EUR'])->willReturn(null);

        $this->getCurrency()->shouldReturn(null);
    }

    function it_calls_the_repository_only_once(
        RepositoryInterface $currencyRepository,
        CurrencyInterface $currency
    ) {
        $currencyRepository->findOneBy(['code' => 'EUR'])->shouldBeCalledTimes(1)->willReturn($currency);

        $this->getCurrency()->shouldReturn($currency);
        $this->getCurrency()->shouldReturn($currency);
    }
}
