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

namespace spec\Sylius\Component\Core\Statistics\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;

final class BusinessActivitySummaryProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $this->beConstructedWith($orderRepository, $customerRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BusinessActivitySummaryProviderInterface::class);
    }

    function it_provides_business_activity_summary(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelInterface $channel,
        \DatePeriod $datePeriod,
    ): void {
        $startDate = new \DateTime('01-02-2022');
        $endDate = new \DateTime('01-12-2022');

        $datePeriod->getStartDate()->willReturn($startDate);
        $datePeriod->getEndDate()->willReturn($endDate);

        $orderRepository
            ->getTotalPaidSalesForChannelInPeriod($channel, Argument::any(), Argument::any())
            ->willReturn(1000);

        $orderRepository
            ->countPaidForChannelInPeriod($channel, Argument::any(), Argument::any())
            ->willReturn(13);

        $customerRepository
            ->countCustomersInPeriod(Argument::any(), Argument::any())
            ->willReturn(4);

        $this->provide($datePeriod, $channel)->shouldBeLike(
            new BusinessActivitySummary(1000, 13, 4),
        );
    }
}
