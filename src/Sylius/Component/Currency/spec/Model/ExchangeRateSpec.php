<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Model;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRate;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Model\ExchangeRateInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ExchangeRateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ExchangeRate::class);
    }

    function it_implements_exchange_rate_interface()
    {
        $this->shouldImplement(ExchangeRateInterface::class);
    }

    function it_throws_an_invalid_argument_exception_when_adding_non_float_ratio()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setRatio', ['1.01']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setRatio', ['asd']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setRatio', [[]]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setRatio', [false]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setRatio', [new \stdClass()]);
    }

    function it_has_a_ratio()
    {
        $this->getRatio()->shouldReturn(null);
        $this->setRatio(1.02);
        $this->getRatio()->shouldReturn(1.02);
        $this->setRatio(1e-6);
        $this->getRatio()->shouldReturn(1e-6);
    }

    function it_has_base_currency(CurrencyInterface $currency)
    {
        $this->getSourceCurrency()->shouldReturn(null);
        $this->setSourceCurrency($currency);
        $this->getSourceCurrency()->shouldReturn($currency);
    }

    function it_has_target_currency(CurrencyInterface $currency)
    {
        $this->getTargetCurrency()->shouldReturn(null);
        $this->setTargetCurrency($currency);
        $this->getTargetCurrency()->shouldReturn($currency);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
