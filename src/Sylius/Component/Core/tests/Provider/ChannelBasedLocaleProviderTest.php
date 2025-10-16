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

namespace Tests\Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\ChannelBasedLocaleProvider;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ChannelBasedLocaleProviderTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private ChannelInterface&MockObject $channel;

    private LocaleInterface&MockObject $locale;

    private ChannelBasedLocaleProvider $provider;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->provider = new ChannelBasedLocaleProvider($this->channelContext, 'pl_PL');
    }

    public function testShouldImplementLocaleProviderInterface(): void
    {
        $this->assertInstanceOf(LocaleProviderInterface::class, $this->provider);
    }

    public function testShouldReturnAllChannelsLocalesAsAvailableOnes(): void
    {
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getLocales')->willReturn(new ArrayCollection([$this->locale]));
        $this->locale->expects($this->once())->method('getCode')->willReturn('en_US');

        $this->assertEquals(['en_US'], $this->provider->getAvailableLocalesCodes());
    }

    public function testShouldReturnDefaultLocaleAsTheAvailableOneIfChannelCannotBeDetermined(): void
    {
        $this->channelContext->expects($this->once())->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->assertEquals(['pl_PL'], $this->provider->getAvailableLocalesCodes());
    }

    public function testShouldReturnChannelDefaultLocale(): void
    {
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getDefaultLocale')->willReturn($this->locale);
        $this->locale->expects($this->once())->method('getCode')->willReturn('en_US');

        $this->assertSame('en_US', $this->provider->getDefaultLocaleCode());
    }

    public function testShouldReturnTheDefaultLocaleIfChannelCannotBeDetermined(): void
    {
        $this->channelContext->expects($this->once())->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->assertSame('pl_PL', $this->provider->getDefaultLocaleCode());
    }
}
