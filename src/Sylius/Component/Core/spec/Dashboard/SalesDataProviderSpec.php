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

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\SalesSummary;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\SalesSummaryProvider;

final class SalesDataProviderSpec extends ObjectBehavior
{
    function let(SalesSummaryProvider $salesSummaryProvider): void
    {
        $this->beConstructedWith($salesSummaryProvider);
    }

    function it_provides_sales_summary(
        SalesSummaryProvider $salesSummaryProvider,
        ChannelInterface $channel
    ): void {
        $salesSummaryProvider
            ->getSalesSummary($channel, new \DateTime('yesterday'), new \DateTime())
            ->willReturn(['06.19' => 7000, '09.19' => 5000])
        ;

        $this
            ->getLastYearSalesSummary($channel)
            ->shouldBeLike(new SalesSummary(new \DateTime('yesterday'), new \DateTime('now'), ['06.19' => 7000, '09.19' => 5000]))
        ;
    }
}
