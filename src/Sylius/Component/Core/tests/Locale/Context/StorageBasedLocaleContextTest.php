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

namespace Tests\Sylius\Component\Core\Locale\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\Context\StorageBasedLocaleContext;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Resource\Exception\StorageUnavailableException;

final class StorageBasedLocaleContextTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private LocaleStorageInterface&MockObject $localeStorage;

    private LocaleProviderInterface&MockObject $localeProvider;

    private ChannelInterface&MockObject $channel;

    private StorageBasedLocaleContext $context;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->localeStorage = $this->createMock(LocaleStorageInterface::class);
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->context = new StorageBasedLocaleContext(
            $this->channelContext,
            $this->localeStorage,
            $this->localeProvider,
        );
    }

    public function testShouldImplementLocaleContextInterface(): void
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->context);
    }

    public function testShouldReturnAvailableActiveLocale(): void
    {
        $this->localeProvider->method('getAvailableLocalesCodes')->willReturn(['pl_PL', 'en_US']);
        $this->channelContext->method('getChannel')->willReturn($this->channel);
        $this->localeStorage->method('get')->with($this->channel)->willReturn('pl_PL');

        $this->assertSame('pl_PL', $this->context->getLocaleCode());
    }

    public function testShouldThrowExceptionWhenChannelCannotBeFound(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->localeProvider->method('getAvailableLocalesCodes')->willReturn(['pl_PL', 'en_US']);
        $this->channelContext->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->context->getLocaleCode();
    }

    public function testShouldThrowExceptionWhenStorageIsUnavailable(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->localeProvider->method('getAvailableLocalesCodes')->willReturn(['pl_PL', 'en_US']);
        $this->channelContext->method('getChannel')->willReturn($this->channel);
        $this->localeStorage->method('get')->with($this->channel)->willThrowException(new StorageUnavailableException());

        $this->context->getLocaleCode();
    }

    public function testShouldThrowExceptionIfStoredLocaleIsUnavailable(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->localeProvider->method('getAvailableLocalesCodes')->willReturn(['en_US', 'en_GB']);
        $this->channelContext->method('getChannel')->willReturn($this->channel);
        $this->localeStorage->method('get')->with($this->channel)->willReturn('pl_PL');

        $this->context->getLocaleCode();
    }
}
