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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultStoreDataInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContextSpec extends ObjectBehavior
{
    function let(
        DefaultStoreDataInterface $defaultFranceChannelFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->beConstructedWith($defaultFranceChannelFactory, $sharedStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ChannelContext');
    }

    function it_is_context()
    {
        $this->shouldImplement('Behat\Behat\Context\Context');
    }

    function it_sets_default_france_channel_in_to_shared_storage(
        $defaultFranceChannelFactory,
        $sharedStorage,
        ChannelInterface $channel,
        ZoneInterface $zone
    ) {
        $defaultData = ['channel' => $channel, 'zone' => $zone];
        $defaultFranceChannelFactory->create()->willReturn($defaultData);
        $sharedStorage->setClipboard($defaultData)->shouldBeCalled();

        $this->thatStoreIsOperatingOnASingleFranceChannel();
    }
}
