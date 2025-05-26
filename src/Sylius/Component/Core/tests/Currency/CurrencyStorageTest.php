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

namespace Tests\Sylius\Component\Core\Currency;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Currency\CurrencyStorage;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Resource\Storage\StorageInterface;

final class CurrencyStorageTest extends TestCase
{
    private MockObject&StorageInterface $storage;

    private ChannelInterface&MockObject $channel;

    private Currency $usd;

    private Currency $eur;

    private CurrencyStorage $currencyStorage;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->usd = new Currency();
        $this->usd->setCode('USD');
        $this->eur = new Currency();
        $this->eur->setCode('EUR');
        $this->currencyStorage = new CurrencyStorage($this->storage);
    }

    public function testShouldImplementCurrencyStorageInterface(): void
    {
        $this->assertInstanceOf(CurrencyStorageInterface::class, $this->currencyStorage);
    }

    public function testShouldGetCurrencyForGivenChannel(): void
    {
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->once())->method('get')->with('_currency_web')->willReturn('BTC');

        $this->assertSame('BTC', $this->currencyStorage->get($this->channel));
    }

    public function testShouldSetCurrencyForGivenChannelIfItIsAvailableAndNotBase(): void
    {
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->usd);
        $this->channel->expects($this->once())->method('getCurrencies')->willReturn(new ArrayCollection([
            $this->usd,
            $this->eur,
        ]));
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->once())->method('set')->with('_currency_web', 'EUR');

        $this->currencyStorage->set($this->channel, 'EUR');
    }

    public function testShouldRemoveCurrencyForGivenChannelIfItIsBase(): void
    {
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->usd);
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->never())->method('set')->with('_currency_web', 'USD');
        $this->storage->expects($this->once())->method('remove')->with('_currency_web');

        $this->currencyStorage->set($this->channel, 'USD');
    }

    public function testShouldRemoveCurrencyIfItIsNotAvailable(): void
    {
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->usd);
        $this->channel->expects($this->once())->method('getCurrencies')->willReturn(new ArrayCollection([
            $this->usd,
            $this->eur,
        ]));
        $this->channel->expects($this->once())->method('getCode')->willReturn('web');
        $this->storage->expects($this->never())->method('set')->with('_currency_web', 'GBP');
        $this->storage->expects($this->once())->method('remove')->with('_currency_web');

        $this->currencyStorage->set($this->channel, 'GBP');
    }
}
