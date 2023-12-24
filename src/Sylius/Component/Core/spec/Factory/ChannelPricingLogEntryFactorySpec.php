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

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ChannelPricingLogEntryFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ChannelPricingLogEntry::class);
    }

    function it_throws_an_exception_when_invalid_class_name_is_passed(): void
    {
        $this->beConstructedWith(ResourceInterface::class);

        $this->shouldThrow(\DomainException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_create_new_is_called(): void
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    function it_creates_a_channel_pricing_log_entry(ChannelPricingInterface $channelPricing): void
    {
        $date = new \DateTimeImmutable();
        $price = 1000;
        $originalPrice = 2000;

        $this
            ->create($channelPricing, $date, $price, $originalPrice)
            ->shouldBeLike(new ChannelPricingLogEntry(
                $channelPricing->getWrappedObject(),
                $date,
                $price,
                $originalPrice,
            ))
        ;
    }
}
