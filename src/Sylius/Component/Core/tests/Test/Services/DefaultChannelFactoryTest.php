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

namespace Tests\Sylius\Component\Core\Test\Services;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactory;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class DefaultChannelFactoryTest extends TestCase
{
    private ChannelFactoryInterface&MockObject $channelFactory;

    private FactoryInterface&MockObject $currencyFactory;

    private FactoryInterface&MockObject $localeFactory;

    private MockObject&RepositoryInterface $channelRepository;

    private MockObject&RepositoryInterface $currencyRepository;

    private MockObject&RepositoryInterface $localeRepository;

    private FactoryInterface&MockObject $shopBillingDataFactory;

    private DefaultChannelFactory $defaultChannelFactory;

    protected function setUp(): void
    {
        $this->channelFactory = $this->createMock(ChannelFactoryInterface::class);
        $this->currencyFactory = $this->createMock(FactoryInterface::class);
        $this->localeFactory = $this->createMock(FactoryInterface::class);
        $this->channelRepository = $this->createMock(RepositoryInterface::class);
        $this->currencyRepository = $this->createMock(RepositoryInterface::class);
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->shopBillingDataFactory = $this->createMock(FactoryInterface::class);
        $this->defaultChannelFactory = new DefaultChannelFactory(
            $this->channelFactory,
            $this->currencyFactory,
            $this->localeFactory,
            $this->shopBillingDataFactory,
            $this->channelRepository,
            $this->currencyRepository,
            $this->localeRepository,
            'en_US',
        );
    }

    public function testShouldImplementDefaultChannelFactoryInterface(): void
    {
        $this->assertInstanceOf(DefaultChannelFactoryInterface::class, $this->defaultChannelFactory);
    }

    public function testShouldCreateDefaultChannelAndPersistIt(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $locale = $this->createMock(LocaleInterface::class);
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);
        $this->localeFactory->expects($this->once())->method('createNew')->willReturn($locale);
        $locale->expects($this->once())->method('setCode')->with('en_US');
        $this->currencyFactory->expects($this->once())->method('createNew')->willReturn($currency);
        $currency->expects($this->once())->method('setCode')->with('USD');
        $this->shopBillingDataFactory->expects($this->once())->method('createNew')->willReturn($shopBillingData);
        $this->channelFactory->expects($this->once())->method('createNamed')->with('Default')->willReturn($channel);
        $channel->expects($this->once())->method('setCode')->with('DEFAULT');
        $channel->expects($this->once())->method('setTaxCalculationStrategy')->with('order_items_based');
        $channel->expects($this->once())->method('addCurrency')->with($currency);
        $channel->expects($this->once())->method('setBaseCurrency')->with($currency);
        $channel->expects($this->once())->method('addLocale')->with($locale);
        $channel->expects($this->once())->method('setDefaultLocale')->with($locale);
        $channel->expects($this->once())->method('getShopBillingData')->willReturn(null);
        $channel->expects($this->once())->method('setShopBillingData')->with($shopBillingData);
        $this->currencyRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'USD'])
            ->willReturn(null);
        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'en_US'])
            ->willReturn(null);
        $this->currencyRepository->expects($this->once())->method('add')->with($currency);
        $this->localeRepository->expects($this->once())->method('add')->with($locale);
        $this->channelRepository->expects($this->once())->method('add')->with($channel);

        $this->assertEquals(
            ['channel' => $channel, 'currency' => $currency, 'locale' => $locale],
            $this->defaultChannelFactory->create(),
        );
    }
}
