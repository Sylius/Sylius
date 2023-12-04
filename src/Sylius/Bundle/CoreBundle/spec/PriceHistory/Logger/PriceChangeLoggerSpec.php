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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\Logger;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class PriceChangeLoggerSpec extends ObjectBehavior
{
    function let(
        ChannelPricingLogEntryFactoryInterface $logEntryFactory,
        ObjectManager $logEntryManager,
        DateTimeProviderInterface $dateTimeProvider,
    ): void {
        $this->beConstructedWith($logEntryFactory, $logEntryManager, $dateTimeProvider);
    }

    function it_implements_price_change_logger_interface(): void
    {
        $this->shouldImplement(PriceChangeLoggerInterface::class);
    }

    function it_throws_exception_when_there_is_no_price(
        ChannelPricingLogEntryFactoryInterface $logEntryFactory,
        ObjectManager $logEntryManager,
        DateTimeProviderInterface $dateTimeProvider,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getPrice()->willReturn(null);

        $dateTimeProvider->now()->shouldNotBeCalled();
        $logEntryFactory->create(Argument::cetera())->shouldNotBeCalled();
        $logEntryManager->persist(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('log', [$channelPricing]);
    }

    function it_logs_price_change(
        ChannelPricingLogEntryFactoryInterface $logEntryFactory,
        ObjectManager $logEntryManager,
        DateTimeProviderInterface $dateTimeProvider,
        ChannelPricingInterface $channelPricing,
        ChannelPricingLogEntryInterface $logEntry,
    ): void {
        $date = new \DateTimeImmutable();
        $price = 1000;
        $originalPrice = 1200;

        $channelPricing->getPrice()->willReturn($price);
        $channelPricing->getOriginalPrice()->willReturn($originalPrice);

        $dateTimeProvider->now()->willReturn($date);

        $logEntryFactory
            ->create($channelPricing, $date, $price, $originalPrice)
            ->shouldBeCalled()
            ->willReturn($logEntry)
        ;

        $logEntryManager->persist($logEntry)->shouldBeCalled();

        $this->log($channelPricing);
    }
}
