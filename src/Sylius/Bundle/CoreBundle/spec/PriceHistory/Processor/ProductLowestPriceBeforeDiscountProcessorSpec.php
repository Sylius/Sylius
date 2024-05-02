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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\Processor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface;

final class ProductLowestPriceBeforeDiscountProcessorSpec extends ObjectBehavior
{
    function let(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $this->beConstructedWith($channelPricingLogEntryRepository, $channelRepository);
    }

    function it_implements_product_lowest_price_processor_interface(): void
    {
        $this->shouldImplement(ProductLowestPriceBeforeDiscountProcessorInterface::class);
    }

    function it_sets_lowest_price_before_discount_to_null_if_original_price_is_null(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getOriginalPrice()->willReturn(null);
        $channelPricing->getPrice()->willReturn(2100);

        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLatestOneByChannelPricing($channelPricing)->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLowestPriceInPeriod(Argument::cetera())->shouldNotBeCalled();

        $channelPricing->getChannelCode()->shouldNotBeCalled();
        $channelPricing->setLowestPriceBeforeDiscount(null)->shouldBeCalled();

        $this->process($channelPricing);
    }

    function it_sets_lowest_price_before_discount_to_null_if_price_is_equal_original_price(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getOriginalPrice()->willReturn(2100);
        $channelPricing->getPrice()->willReturn(2100);

        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLatestOneByChannelPricing($channelPricing)->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLowestPriceInPeriod(Argument::cetera())->shouldNotBeCalled();

        $channelPricing->getChannelCode()->shouldNotBeCalled();
        $channelPricing->setLowestPriceBeforeDiscount(null)->shouldBeCalled();

        $this->process($channelPricing);
    }

    function it_sets_lowest_price_before_discount_to_null_if_price_is_greater_than_original_price(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getOriginalPrice()->willReturn(2100);
        $channelPricing->getPrice()->willReturn(3700);

        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLatestOneByChannelPricing($channelPricing)->shouldNotBeCalled();
        $channelPricingLogEntryRepository->findLowestPriceInPeriod(Argument::cetera())->shouldNotBeCalled();

        $channelPricing->getChannelCode()->shouldNotBeCalled();
        $channelPricing->setLowestPriceBeforeDiscount(null)->shouldBeCalled();

        $this->process($channelPricing);
    }

    function it_sets_lowest_price_before_discount_to_null_if_there_is_no_log_entries(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channel->getChannelPriceHistoryConfig()->willReturn($channelPriceHistoryConfig);

        $channelPricing->getOriginalPrice()->willReturn(3700);
        $channelPricing->getPrice()->willReturn(2100);
        $channelPricing->getChannelCode()->willReturn('WEB');
        $channelPriceHistoryConfig->getLowestPriceForDiscountedProductsCheckingPeriod()->shouldNotBeCalled();

        $channelRepository->findOneByCode('WEB')->willReturn($channel);
        $channelPricingLogEntryRepository->findLatestOneByChannelPricing($channelPricing)->willReturn(null);
        $channelPricingLogEntryRepository->findLowestPriceInPeriod(Argument::cetera())->shouldNotBeCalled();

        $channelPricing->setLowestPriceBeforeDiscount(null)->shouldBeCalled();

        $this->process($channelPricing);
    }

    function it_sets_lowest_price_before_discount_to_lowest_price_found_in_the_given_period_if_price_is_less_than_original_price(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntryRepository,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig,
        ChannelPricingInterface $channelPricing,
        ChannelPricingLogEntry $latestLogEntry,
    ): void {
        $channel->getChannelPriceHistoryConfig()->willReturn($channelPriceHistoryConfig);

        $channelPricing->getOriginalPrice()->willReturn(3700);
        $channelPricing->getPrice()->willReturn(2100);
        $channelPricing->getChannelCode()->willReturn('WEB');

        $channelRepository->findOneByCode('WEB')->willReturn($channel);
        $channelPriceHistoryConfig->getLowestPriceForDiscountedProductsCheckingPeriod()->willReturn(30);

        $unformattedDate = new \DateTimeImmutable();
        $latestLogEntry->getLoggedAt()->willReturn($unformattedDate);
        $loggedAt = new \DateTimeImmutable($unformattedDate->format('Y-m-d H:i:s'));
        $startDate = $loggedAt->sub(new \DateInterval(sprintf('P%dD', 30)));

        $latestLogEntry->getChannelPricing()->willReturn($channelPricing);
        $latestLogEntry->getLoggedAt()->willReturn($loggedAt);
        $latestLogEntry->getId()->willReturn(1234);

        $channelPricingLogEntryRepository->findLatestOneByChannelPricing($channelPricing)->willReturn($latestLogEntry);
        $channelPricingLogEntryRepository
            ->findLowestPriceInPeriod(1234, $channelPricing, $startDate)
            ->willReturn(6900)
        ;

        $channelPricing->setLowestPriceBeforeDiscount(6900)->shouldBeCalled();

        $this->process($channelPricing);
    }
}
