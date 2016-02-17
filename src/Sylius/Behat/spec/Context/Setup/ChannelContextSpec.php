<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $franceChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $franceChannelFactory,
            $defaultChannelFactory,
            $channelFactory,
            $channelRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ChannelContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_sets_default_france_channel_in_the_shared_storage(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $franceChannelFactory,
        ChannelInterface $channel,
        ZoneInterface $zone
    ) {
        $defaultData = ['channel' => $channel, 'zone' => $zone];
        $franceChannelFactory->create()->willReturn($defaultData);
        $sharedStorage->setClipboard($defaultData)->shouldBeCalled();

        $this->storeOperatesOnASingleChannelInFrance();
    }

    function it_sets_default_channel_in_the_shared_storage(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $defaultChannelFactory,
        ChannelInterface $channel,
        ZoneInterface $zone
    ) {
        $defaultData = ['channel' => $channel, 'zone' => $zone];
        $defaultChannelFactory->create()->willReturn($defaultData);
        $sharedStorage->setClipboard($defaultData)->shouldBeCalled();

        $this->storeOperatesOnASingleChannel();
    }
}
