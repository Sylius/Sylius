<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorage;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin SharedStorage
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SharedStorageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\SharedStorage');
    }

    function it_implements_shared_storage_interface()
    {
        $this->shouldImplement(SharedStorageInterface::class);
    }

    function it_has_resources_in_clipboard(ChannelInterface $channel, ProductInterface $product)
    {
        $this->set('channel1', $channel);
        $this->get('channel1')->shouldReturn($channel);

        $this->set('product1', $product);
        $this->get('product1')->shouldReturn($product);
    }

    function it_returns_latest_added_resource(ChannelInterface $channel, ProductInterface $product)
    {
        $this->set('channel1', $channel);
        $this->set('product1', $product);

        $this->getLatestResource()->shouldReturn($product);
    }

    function it_overrides_existing_resource_key(ChannelInterface $firstChannel, ChannelInterface $secondChannel)
    {
        $this->set('channel', $firstChannel);
        $this->set('channel', $secondChannel);

        $this->get('channel')->shouldReturn($secondChannel);
    }

    function its_clipboard_can_be_set(ChannelInterface $channel)
    {
        $this->setClipboard(['channel' => $channel]);

        $this->get('channel')->shouldReturn($channel);
    }

    function it_checks_if_resource_under_given_key_exist(ChannelInterface $channel)
    {
        $this->setClipboard(['channel' => $channel]);

        $this->has('channel')->shouldReturn(true);
    }
}
