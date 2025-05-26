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
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Core\Test\Services\DefaultUnitedStatesChannelFactory;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class DefaultUnitedStatesChannelFactoryTest extends TestCase
{
    private MockObject&RepositoryInterface $channelRepository;

    private MockObject&RepositoryInterface $countryRepository;

    private MockObject&RepositoryInterface $currencyRepository;

    private MockObject&RepositoryInterface $localeRepository;

    private MockObject&RepositoryInterface $zoneRepository;

    private ChannelFactoryInterface&MockObject $channelFactory;

    private FactoryInterface&MockObject $countryFactory;

    private FactoryInterface&MockObject $currencyFactory;

    private FactoryInterface&MockObject $localeFactory;

    private MockObject&ZoneFactoryInterface $zoneFactory;

    private DefaultUnitedStatesChannelFactory $defaultChannelFactory;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(RepositoryInterface::class);
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->currencyRepository = $this->createMock(RepositoryInterface::class);
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->zoneRepository = $this->createMock(RepositoryInterface::class);
        $this->channelFactory = $this->createMock(ChannelFactoryInterface::class);
        $this->countryFactory = $this->createMock(FactoryInterface::class);
        $this->currencyFactory = $this->createMock(FactoryInterface::class);
        $this->localeFactory = $this->createMock(FactoryInterface::class);
        $this->zoneFactory = $this->createMock(ZoneFactoryInterface::class);
        $this->defaultChannelFactory = new DefaultUnitedStatesChannelFactory(
            $this->channelRepository,
            $this->countryRepository,
            $this->currencyRepository,
            $this->localeRepository,
            $this->zoneRepository,
            $this->channelFactory,
            $this->countryFactory,
            $this->currencyFactory,
            $this->localeFactory,
            $this->zoneFactory,
            'en_US',
        );
    }

    public function testShouldImplementDefaultChannelFactoryInterface(): void
    {
        $this->assertInstanceOf(DefaultChannelFactoryInterface::class, $this->defaultChannelFactory);
    }

    public function testShouldCreateDefaultUnitedStatesChannelWithCountryZoneAndUsdAsBaseCurrency(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $unitedStates = $this->createMock(CountryInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $locale = $this->createMock(LocaleInterface::class);
        $this->channelFactory->expects($this->once())->method('createNamed')->with('United States')->willReturn($channel);
        $this->localeFactory->expects($this->once())->method('createNew')->willReturn($locale);
        $locale->expects($this->once())->method('setCode')->with('en_US');
        $this->zoneFactory->expects($this->once())->method('createWithMembers')->with(['US'])->willReturn($zone);
        $channel->expects($this->once())->method('setCode')->with('WEB-US');
        $channel->expects($this->once())->method('setTaxCalculationStrategy')->with('order_items_based');
        $zone->expects($this->once())->method('setCode')->with('US');
        $zone->expects($this->once())->method('setName')->with('United States');
        $zone->expects($this->once())->method('setType')->with(ZoneInterface::TYPE_COUNTRY);
        $this->countryFactory->expects($this->once())->method('createNew')->willReturn($unitedStates);
        $unitedStates->expects($this->once())->method('setCode')->with('US');
        $this->currencyFactory->expects($this->once())->method('createNew')->willReturn($currency);
        $currency->expects($this->once())->method('setCode')->with('USD');
        $channel->expects($this->once())->method('setBaseCurrency')->with($currency);
        $channel->expects($this->once())->method('addCurrency')->with($currency);
        $channel->expects($this->once())->method('setDefaultLocale')->with($locale);
        $channel->expects($this->once())->method('addLocale')->with($locale);
        $channel->expects($this->once())->method('setHostname')->with('us.store.com');
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
        $this->countryRepository->expects($this->once())->method('add')->with($unitedStates);
        $this->zoneRepository->expects($this->once())->method('add')->with($zone);

        $this->assertEquals(
            ['channel' => $channel, 'currency' => $currency, 'locale' => $locale, 'country' => $unitedStates, 'zone' => $zone],
            $this->defaultChannelFactory->create(),
        );
    }
}
