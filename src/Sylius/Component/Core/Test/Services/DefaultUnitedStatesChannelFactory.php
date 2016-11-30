<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class DefaultUnitedStatesChannelFactory implements DefaultChannelFactoryInterface
{
    const DEFAULT_CHANNEL_CODE = 'WEB-US';
    const DEFAULT_COUNTRY_CODE = 'US';
    const DEFAULT_ZONE_CODE = 'US';
    const DEFAULT_CURRENCY_CODE = 'USD';
    const DEFAULT_ZONE_NAME = 'United States';
    const DEFAULT_CHANNEL_NAME = 'United States';

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneMemberRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @var FactoryInterface
     */
    private $localeFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneMemberFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneFactory;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $currencyRepository
     * @param RepositoryInterface $localeRepository
     * @param RepositoryInterface $zoneMemberRepository
     * @param RepositoryInterface $zoneRepository
     * @param ChannelFactoryInterface $channelFactory
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $currencyFactory
     * @param FactoryInterface $localeFactory
     * @param FactoryInterface $zoneFactory
     * @param FactoryInterface $zoneMemberFactory
     * @param string $defaultLocaleCode
     */
    public function __construct(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory,
        $defaultLocaleCode
    ) {
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->currencyRepository = $currencyRepository;
        $this->localeRepository = $localeRepository;
        $this->zoneMemberRepository = $zoneMemberRepository;
        $this->zoneRepository = $zoneRepository;
        $this->channelFactory = $channelFactory;
        $this->countryFactory = $countryFactory;
        $this->currencyFactory = $currencyFactory;
        $this->localeFactory = $localeFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->zoneFactory = $zoneFactory;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function create($code = null, $name = null, $currencyCode = null)
    {
        $currency = $this->provideCurrency($currencyCode);
        $locale = $this->provideLocale();

        $channel = $this->createChannel($code ?: self::DEFAULT_CHANNEL_CODE, $name ?: self::DEFAULT_CHANNEL_NAME);
        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);
        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);
        $channel->setTaxCalculationStrategy('order_items_based');

        $defaultData['channel'] = $channel;
        $defaultData['country'] = $this->createCountry();
        $defaultData['currency'] = $currency;
        $defaultData['locale'] = $locale;
        $defaultData['zone_member'] = $this->createZoneMember();
        $defaultData['zone'] = $this->createZone($defaultData['zone_member']);

        $this->channelRepository->add($channel);
        $this->countryRepository->add($defaultData['country']);
        $this->zoneRepository->add($defaultData['zone']);
        $this->zoneMemberRepository->add($defaultData['zone_member']);

        return $defaultData;
    }

    /**
     * @return ChannelInterface
     */
    private function createChannel($code, $name)
    {
        $channel = $this->channelFactory->createNamed($name);
        $channel->setCode($code);

        return $channel;
    }

    /**
     * @return CountryInterface
     */
    private function createCountry()
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode(self::DEFAULT_COUNTRY_CODE);

        return $country;
    }

    /**
     * @return CurrencyInterface
     */
    private function provideCurrency($currencyCode = null)
    {
        $currencyCode = $currencyCode ?: self::DEFAULT_CURRENCY_CODE;

        /** @var CurrencyInterface $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);

        if (null === $currency) {
            $currency = $this->currencyFactory->createNew();
            $currency->setCode($currencyCode);

            $this->currencyRepository->add($currency);
        }

        return $currency;
    }

    /**
     * @return LocaleInterface
     */
    private function provideLocale()
    {
        /** @var LocaleInterface $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $this->defaultLocaleCode]);

        if (null === $locale) {
            $locale = $this->localeFactory->createNew();
            $locale->setCode($this->defaultLocaleCode);

            $this->localeRepository->add($locale);
        }

        return $locale;
    }

    /**
     * @return ZoneMemberInterface
     */
    private function createZoneMember()
    {
        /** @var ZoneMemberInterface $zoneMember */
        $zoneMember = $this->zoneMemberFactory->createNew();
        $zoneMember->setCode(self::DEFAULT_ZONE_CODE);

        return $zoneMember;
    }

    /**
     * @param ZoneMemberInterface $zoneMember
     *
     * @return ZoneInterface
     */
    private function createZone(ZoneMemberInterface $zoneMember)
    {
        /** @var ZoneInterface $zone */
        $zone = $this->zoneFactory->createNew();
        $zone->setCode(self::DEFAULT_ZONE_CODE);
        $zone->setName(self::DEFAULT_ZONE_NAME);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->addMember($zoneMember);

        return $zone;
    }
}
