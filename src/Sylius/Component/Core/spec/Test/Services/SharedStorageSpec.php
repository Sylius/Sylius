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

/**
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
        $this->shouldImplement('Sylius\Component\Core\Test\Services\SharedStorageInterface');
    }

    function it_has_resources_in_clipboard(ChannelInterface $channel, ProductInterface $product)
    {
        $this->setCurrentResource('channel1', $channel);
        $this->getCurrentResource('channel1')->shouldReturn($channel);

        $this->setCurrentResource('product1', $product);
        $this->getCurrentResource('product1')->shouldReturn($product);
    }

    function it_returns_latest_added_resource(ChannelInterface $channel, ProductInterface $product)
    {
        $this->setCurrentResource('channel1', $channel);
        $this->setCurrentResource('product1', $product);
        $this->getLatestResource()->shouldReturn($product);
    }

    function it_prevents_setting_resource_at_used_key(ChannelInterface $channel, ChannelInterface $channel1)
    {
        $this->setCurrentResource('channel', $channel);

        $this->shouldThrow(new \RuntimeException('This key is already used, if you want override set override flag'))->during('setCurrentResource', array('channel', $channel1));
    }

    function it_overrides_existing_resource_key(ChannelInterface $channel, ChannelInterface $channel1)
    {
        $this->setCurrentResource('channel', $channel);
        $this->setCurrentResource('channel', $channel1, true);

        $this->getCurrentResource('channel')->shouldReturn($channel1);
    }

    function it_prevents_setting_clipboard_if_it_is_not_empty(ChannelInterface $channel)
    {
        $this->setCurrentResource('channel', $channel);

        $this->shouldThrow(new \RuntimeException('Clipboard is not empty, if you want override set override flag'))->during('setClipboard', array(array()));
    }

    function it_overrides_clipboard(ChannelInterface $channel)
    {
        $this->setCurrentResource('channel', $channel);

        $this->setClipboard(array(), true);
    }
}
