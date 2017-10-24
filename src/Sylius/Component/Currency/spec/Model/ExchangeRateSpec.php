<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Currency\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;

final class ExchangeRateSpec extends ObjectBehavior
{
    function it_implements_exchange_rate_interface(): void
    {
        $this->shouldImplement(ExchangeRateInterface::class);
    }

    function it_has_a_ratio(): void
    {
        $this->getRatio()->shouldReturn(null);
        $this->setRatio(1.02);
        $this->getRatio()->shouldReturn(1.02);
        $this->setRatio(1e-6);
        $this->getRatio()->shouldReturn(1e-6);
    }

    function it_has_base_currency(CurrencyInterface $currency): void
    {
        $this->getSourceCurrency()->shouldReturn(null);
        $this->setSourceCurrency($currency);
        $this->getSourceCurrency()->shouldReturn($currency);
    }

    function it_has_target_currency(CurrencyInterface $currency): void
    {
        $this->getTargetCurrency()->shouldReturn(null);
        $this->setTargetCurrency($currency);
        $this->getTargetCurrency()->shouldReturn($currency);
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function its_creation_date_is_mutable(\DateTime $date): void
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date): void
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
