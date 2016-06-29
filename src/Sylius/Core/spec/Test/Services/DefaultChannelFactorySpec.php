<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Channel\Factory\ChannelFactoryInterface;
use Sylius\Core\Model\ChannelInterface;
use Sylius\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultChannelFactorySpec extends ObjectBehavior
{
    function let(ChannelFactoryInterface $channelFactory, RepositoryInterface $channelRepository)
    {
        $this->beConstructedWith($channelFactory, $channelRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Test\Services\DefaultChannelFactory');
    }

    function it_implements_default_channel_factory_interface()
    {
        $this->shouldImplement(DefaultChannelFactoryInterface::class);
    }

    function it_creates_default_channel_and_persist_it(
        $channelFactory,
        $channelRepository,
        ChannelInterface $channel
    ) {
        $channelFactory->createNamed('Default')->willReturn($channel);

        $channel->setCode('DEFAULT')->shouldBeCalled();
        $channelRepository->add($channel)->shouldBeCalled();

        $this->create()->shouldReturn(['channel' => $channel]);
    }
}
