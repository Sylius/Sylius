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

namespace spec\Sylius\Component\Taxation\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateCheckerInterface;

final class TaxRateDateCheckerSpec extends ObjectBehavior
{
    function let(DateTimeProviderInterface $calendar): void
    {
        $this->beConstructedWith($calendar);
    }

    function it_implements_tax_rate_resolver_interface(): void
    {
        $this->shouldImplement(TaxRateDateCheckerInterface::class);
    }

    function it_can_be_in_date_when_both_dates_are_defined(): void
    {
        $startDate = new \DateTime('01-01-2022');
        $endDate = new \DateTime('01-03-2022');

        $this->isInDate(new \DateTime('12-12-2021'), $startDate, $endDate)->shouldReturn(false);
        $this->isInDate(new \DateTime('02-02-2022'), $startDate, $endDate)->shouldReturn(true);
        $this->isInDate(new \DateTime('03-03-2022'), $startDate, $endDate)->shouldReturn(false);
    }

    function it_can_be_in_date_when_one_date_is_defined(): void
    {
        $startDate = new \DateTime('01-01-2022');

        $this->isInDate(new \DateTime('12-12-2021'), $startDate, null)->shouldReturn(false);
        $this->isInDate(new \DateTime('02-02-2022'), $startDate, null)->shouldReturn(true);
    }
}
