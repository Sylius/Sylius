<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Channel\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Channel\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $defaultFactory
    ) {
        $this->beConstructedWith($defaultFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Channel\Factory\ChannelFactory');
    }

    function it_implements_channel_factory_interface()
    {
        $this->shouldImplement('Sylius\Channel\Factory\ChannelFactoryInterface');
    }

    function it_creates_channel_with_name($defaultFactory, ChannelInterface $channel)
    {
        $defaultFactory->createNew()->willReturn($channel);

        $channel->setName('United States Webstore')->shouldBeCalled();

        $this->createNamed('United States Webstore')->shouldReturn($channel);
    }

    function it_creates_empty_channel($defaultFactory, ChannelInterface $channel)
    {
        $defaultFactory->createNew()->willReturn($channel);

        $this->createNew()->shouldReturn($channel);
    }
}
