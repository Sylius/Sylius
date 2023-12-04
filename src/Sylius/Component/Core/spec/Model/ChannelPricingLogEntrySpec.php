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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class ChannelPricingLogEntrySpec extends ObjectBehavior
{
    function let(ChannelPricingInterface $channelPricing): void
    {
        $this->beConstructedWith($channelPricing, new \DateTime(), 1000, 2000);
    }

    function it_implements_channel_pricing_log_entry_interface(): void
    {
        $this->shouldImplement(ChannelPricingLogEntryInterface::class);
    }

    function it_initialize_with_no_original_price(ChannelPricingInterface $channelPricing): void
    {
        $this->beConstructedWith($channelPricing, new \DateTime(), 1000, null);
        $this->getOriginalPrice()->shouldReturn(null);
    }

    function it_gets_a_channel_pricing(): void
    {
        $this->getChannelPricing()->shouldReturnAnInstanceOf(ChannelPricingInterface::class);
    }

    function it_gets_a_price(): void
    {
        $this->getPrice()->shouldReturn(1000);
    }

    function it_gets_an_original_price(): void
    {
        $this->getOriginalPrice()->shouldReturn(2000);
    }

    function it_gets_a_logged_at(): void
    {
        $this->getLoggedAt()->shouldReturnAnInstanceOf(\DateTimeInterface::class);
    }
}
