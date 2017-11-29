<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Behat\Service;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class SharedStorageSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SharedStorage::class);
    }

    public function it_implements_shared_storage_interface(): void
    {
        $this->shouldImplement(SharedStorageInterface::class);
    }

    public function it_has_resources_in_clipboard(ChannelInterface $channel, ProductInterface $product): void
    {
        $this->set('channel1', $channel);
        $this->get('channel1')->shouldReturn($channel);

        $this->set('product1', $product);
        $this->get('product1')->shouldReturn($product);
    }

    public function it_returns_latest_added_resource(ChannelInterface $channel, ProductInterface $product): void
    {
        $this->set('channel1', $channel);
        $this->set('product1', $product);

        $this->getLatestResource()->shouldReturn($product);
    }

    public function it_overrides_existing_resource_key(ChannelInterface $firstChannel, ChannelInterface $secondChannel): void
    {
        $this->set('channel', $firstChannel);
        $this->set('channel', $secondChannel);

        $this->get('channel')->shouldReturn($secondChannel);
    }

    public function its_clipboard_can_be_set(ChannelInterface $channel): void
    {
        $this->setClipboard(['channel' => $channel]);

        $this->get('channel')->shouldReturn($channel);
    }

    public function it_checks_if_resource_under_given_key_exist(ChannelInterface $channel): void
    {
        $this->setClipboard(['channel' => $channel]);

        $this->has('channel')->shouldReturn(true);
    }
}
