<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxation\Checker;

use DateTime;
use PhpSpec\ObjectBehavior;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class TaxRateDateEligibilityCheckerSpec extends ObjectBehavior
{
    function let(DateTimeProviderInterface $calendar): void
    {
        $this->beConstructedWith($calendar);
    }

    function it_implements_tax_rate_resolver_interface(): void
    {
        $this->shouldImplement(TaxRateDateEligibilityCheckerInterface::class);
    }

    function it_can_be_in_date_when_both_dates_are_defined(
        TaxRateInterface $taxRate1,
        TaxRateInterface $taxRate2,
        DateTimeProviderInterface $calendar,
    ): void {
        $calendar->now()->willReturn(new DateTime('01-02-2022'));
        $startDate = new DateTime('01-01-2022');
        $endDate = new DateTime('01-03-2022');
        $taxRate1->getStartDate()->willReturn($startDate);
        $taxRate1->getEndDate()->willReturn($endDate);

        $startDate2 = new DateTime('01-01-2012');
        $endDate2 = new DateTime('21-01-2022');
        $taxRate2->getStartDate()->willReturn($startDate2);
        $taxRate2->getEndDate()->willReturn($endDate2);

        $this->isEligible($taxRate1)->shouldReturn(true);
        $this->isEligible($taxRate2)->shouldReturn(false);
    }

    function it_can_be_in_date_when_only_start_date_is_defined(
        TaxRateInterface $taxRate1,
        TaxRateInterface $taxRate2,
        DateTimeProviderInterface $calendar,
    ): void {
        $calendar->now()->willReturn(new DateTime('01-02-2022'));
        $startDate = new DateTime('01-01-2022');
        $taxRate1->getStartDate()->willReturn($startDate);
        $taxRate1->getEndDate()->willReturn(null);

        $startDate2 = new DateTime('21-09-2029');
        $taxRate2->getStartDate()->willReturn($startDate2);
        $taxRate2->getEndDate()->willReturn(null);

        $this->isEligible($taxRate1)->shouldReturn(true);
        $this->isEligible($taxRate2)->shouldReturn(false);
    }

    function it_can_be_in_date_when_only_end_date_is_defined(
        TaxRateInterface $taxRate1,
        TaxRateInterface $taxRate2,
        DateTimeProviderInterface $calendar,
    ): void {
        $calendar->now()->willReturn(new DateTime('01-02-2022'));
        $endDate = new DateTime('01-01-2022');
        $taxRate1->getStartDate()->willReturn(null);
        $taxRate1->getEndDate()->willReturn($endDate);

        $endDate2 = new DateTime('21-09-2029');
        $taxRate2->getStartDate()->willReturn(null);
        $taxRate2->getEndDate()->willReturn($endDate2);

        $this->isEligible($taxRate1)->shouldReturn(false);
        $this->isEligible($taxRate2)->shouldReturn(true);
    }
}
