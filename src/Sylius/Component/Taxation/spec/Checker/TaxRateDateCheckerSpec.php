<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Taxation\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateCheckerInterface;

class TaxRateDateCheckerSpec extends ObjectBehavior
{
    function let(DateTimeProviderInterface $calendar): void
    {
        $this->beConstructedWith($calendar);
    }

    function it_implements_tax_rate_resolver_interface(): void
    {
        $this->shouldImplement(TaxRateDateCheckerInterface::class);
    }
    function it_returns_null_if_tax_rate_is_not_in_date(
        DateTimeProviderInterface $calendar,
        TaxRateInterface $firstTaxRate,
        TaxRateInterface $secondTaxRate,
        TaxRateInterface $thirdTaxRate
    ): void {
        $now = new \DateTime();
        $calendar->now()->willReturn($now);

        $firstTaxRate->isInDate($now)->willReturn(false);
        $secondTaxRate->isInDate($now)->willReturn(false);
        $thirdTaxRate->isInDate($now)->willReturn(false);

        $this->check([$firstTaxRate, $secondTaxRate, $thirdTaxRate])->shouldReturn(null);
    }

    function it_returns_tax_rate_if_it_is_in_date(
        DateTimeProviderInterface $calendar,
        TaxRateInterface $firstTaxRate,
        TaxRateInterface $secondTaxRate,
        TaxRateInterface $thirdTaxRate
    ): void {
        $now = new \DateTime();
        $calendar->now()->willReturn($now);

        $firstTaxRate->isInDate($now)->willReturn(false);
        $secondTaxRate->isInDate($now)->willReturn(true);
        $thirdTaxRate->isInDate($now)->willReturn(false);

        $this->check([$firstTaxRate, $secondTaxRate, $thirdTaxRate])->shouldReturn($secondTaxRate);
    }
}
