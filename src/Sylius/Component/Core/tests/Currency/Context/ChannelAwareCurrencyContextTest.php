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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\Context\ChannelAwareCurrencyContext;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\Currency;

final class ChannelAwareCurrencyContextTest extends TestCase
{
    private CurrencyContextInterface&MockObject $currencyContext;

    private ChannelContextInterface&MockObject $channelContext;

    private ChannelInterface&MockObject $channel;

    private Currency $eur;

    private ChannelAwareCurrencyContext $context;

    protected function setUp(): void
    {
        $this->currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
        $this->channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
        $this->channel = $this->getMockBuilder(ChannelInterface::class)->getMock();
        $this->eur = new Currency();
        $this->eur->setCode('EUR');
        $this->context = new ChannelAwareCurrencyContext(
            $this->currencyContext,
            $this->channelContext,
        );
    }

    public function testShouldImplementCurrencyContextInterface(): void
    {
        $this->assertInstanceOf(CurrencyContextInterface::class, $this->context);
    }

    public function testShouldReturnTheCurrencyCodeFromDecoratedContextIfItIsAvailableInCurrentChannel(): void
    {
        $usd = new Currency();
        $usd->setCode('USD');

        $this->channel->expects($this->once())->method('getCurrencies')->willReturn(new ArrayCollection([
            $this->eur,
            $usd,
        ]));
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->currencyContext->expects($this->once())->method('getCurrencyCode')->willReturn('USD');

        $this->assertSame('USD', $this->context->getCurrencyCode());
    }

    public function testShouldReturnChannelsBaseCurrencyIfTheOneFromContextIsNotAvailable(): void
    {
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->eur);
        $this->channel->expects($this->once())->method('getCurrencies')->willReturn(new ArrayCollection([$this->eur]));
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->currencyContext->expects($this->once())->method('getCurrencyCode')->willReturn('USD');

        $this->assertSame('EUR', $this->context->getCurrencyCode());
    }

    public function testShouldReturnChannelsBaseCurrencyIfCurrencyWaNotFound(): void
    {
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->eur);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->currencyContext->expects($this->once())->method('getCurrencyCode')->willThrowException(new CurrencyNotFoundException());

        $this->assertSame('EUR', $this->context->getCurrencyCode());
    }
}
