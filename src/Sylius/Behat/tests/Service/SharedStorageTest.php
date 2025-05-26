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

namespace Tests\Sylius\Behat\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class SharedStorageTest extends TestCase
{
    private SharedStorage $sharedStorage;

    protected function setUp(): void
    {
        $this->sharedStorage = new SharedStorage();
    }

    public function testImplementsSharedStorageInterface(): void
    {
        $this->assertInstanceOf(SharedStorageInterface::class, $this->sharedStorage);
    }

    public function testHasResourcesInClipboard(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $this->sharedStorage->set('channel', $channel);
        $this->assertSame($channel, $this->sharedStorage->get('channel'));

        $this->sharedStorage->set('product', $product);
        $this->assertSame($product, $this->sharedStorage->get('product'));
    }

    public function testReturnsLatestAddedResource(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('product', $product);

        $this->assertSame($product, $this->sharedStorage->getLatestResource());
    }

    public function testOverridesExistingResourceKey(): void
    {
        /** @var ChannelInterface&MockObject $firstChannel */
        $firstChannel = $this->createMock(ChannelInterface::class);
        /** @var ChannelInterface&MockObject $secondChannel */
        $secondChannel = $this->createMock(ChannelInterface::class);

        $this->sharedStorage->set('channel', $firstChannel);
        $this->sharedStorage->set('channel', $secondChannel);

        $this->assertSame($secondChannel, $this->sharedStorage->get('channel'));
    }

    public function testItsClipboardCanBeSet(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $this->sharedStorage->setClipboard(['channel' => $channel]);

        $this->assertSame($channel, $this->sharedStorage->get('channel'));
    }

    public function testChecksIfResourceUnderGivenKeyExist(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $this->sharedStorage->setClipboard(['channel' => $channel]);

        $this->assertTrue($this->sharedStorage->has('channel'));
    }

    public function testRemovesExistingKey(): void
    {
        $this->sharedStorage->set('key', 'value');
        $this->sharedStorage->remove('key');

        $this->assertFalse($this->sharedStorage->has('key'));
    }
}
