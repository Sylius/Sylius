<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Channel\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
        $this->shouldHaveType('Sylius\Component\Channel\Factory\ChannelFactory');
    }

    function it_implements_channel_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Channel\Factory\ChannelFactoryInterface');
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
