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

namespace Tests\Sylius\Component\Core\Locale;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\LocaleStorage;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Resource\Storage\StorageInterface;

final class LocaleStorageTest extends TestCase
{
    private MockObject&StorageInterface $storage;

    private ChannelInterface&MockObject $channel;

    private LocaleStorage $localeStorage;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->localeStorage = new LocaleStorage($this->storage);
    }

    public function testShouldImplementLocaleStorageInterface(): void
    {
        $this->assertInstanceOf(LocaleStorageInterface::class, $this->localeStorage);
    }

    public function testShouldSetLocaleForGivenChannel(): void
    {
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->once())->method('set')->with('_locale_web', 'BTC');

        $this->localeStorage->set($this->channel, 'BTC');
    }

    public function testShouldGetLocaleForGivenChannel(): void
    {
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->once())->method('get')->with('_locale_web')->willReturn('BTC');

        $this->assertSame('BTC', $this->localeStorage->get($this->channel));
    }

    public function testShouldThrowExceptionIfLocaleNotFoundForChannel(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->once())->method('get')->with('_locale_web')->willReturn(null);

        $this->localeStorage->get($this->channel);
    }
}
