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

namespace Tests\Sylius\Component\Core\Currency\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\Context\StorageBasedCurrencyContext;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

final class StorageBasedCurrencyContextTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private CurrencyStorageInterface&MockObject $currencyStorage;

    private ChannelInterface&MockObject $channel;

    private StorageBasedCurrencyContext $context;

    protected function setUp(): void
    {
        $this->channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
        $this->currencyStorage = $this->getMockBuilder(CurrencyStorageInterface::class)->getMock();
        $this->channel = $this->getMockBuilder(ChannelInterface::class)->getMock();
        $this->context = new StorageBasedCurrencyContext($this->channelContext, $this->currencyStorage);
    }

    public function testShouldImplementCurrencyContextInterface(): void
    {
        $this->assertInstanceOf(CurrencyContextInterface::class, $this->context);
    }

    public function testShouldReturnAnAvailableActiveCurrency(): void
    {
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->currencyStorage->expects($this->once())->method('get')->with($this->channel)->willReturn('BTC');

        $this->assertSame('BTC', $this->context->getCurrencyCode());
    }

    public function testShouldThrowExceptionIfStorageDoesNotHaveCurrencyCode(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->currencyStorage->expects($this->once())->method('get')->with($this->channel)->willReturn(null);

        $this->context->getCurrencyCode();
    }
}
