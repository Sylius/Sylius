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
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ChannelFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory): void
    {
        $this->beConstructedWith($decoratedFactory, 'order_items_based');
    }

    function it_implements_channel_factory_interface(): void
    {
        $this->shouldImplement(ChannelFactoryInterface::class);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_a_new_channel(FactoryInterface $decoratedFactory, ChannelInterface $channel): void
    {
        $decoratedFactory->createNew()->willReturn($channel);
        $channel->setTaxCalculationStrategy('order_items_based')->shouldBeCalled();

        $this->createNew()->shouldReturn($channel);
    }
}
