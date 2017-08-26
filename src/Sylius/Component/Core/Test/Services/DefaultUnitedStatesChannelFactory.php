<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
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
    public const DEFAULT_CHANNEL_CODE = 'WEB-US';
    public const DEFAULT_COUNTRY_CODE = 'US';
    public const DEFAULT_ZONE_CODE = 'US';
    public const DEFAULT_CURRENCY_CODE = 'USD';
    public const DEFAULT_ZONE_NAME = 'United States';
    public const DEFAULT_CHANNEL_NAME = 'United States';

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
     * @var ZoneFactoryInterface
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
     * @param RepositoryInterface $zoneRepository
     * @param ChannelFactoryInterface $channelFactory
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $currencyFactory
     * @param FactoryInterface $localeFactory
     * @param ZoneFactoryInterface $zoneFactory
     * @param string $defaultLocaleCode
     */
    public function __construct(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        ZoneFactoryInterface $zoneFactory,
        $defaultLocaleCode
    ) {
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->currencyRepository = $currencyRepository;
        $this->localeRepository = $localeRepository;
        $this->zoneRepository = $zoneRepository;
        $this->channelFactory = $channelFactory;
        $this->countryFactory = $countryFactory;
        $this->currencyFactory = $currencyFactory;
        $this->localeFactory = $localeFactory;
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
        $defaultData['zone'] = $this->createZone();

        $this->channelRepository->add($channel);
        $this->countryRepository->add($defaultData['country']);
        $this->zoneRepository->add($defaultData['zone']);

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
     * @return ZoneInterface
     */
    private function createZone()
    {
        /** @var ZoneInterface $zone */
        $zone = $this->zoneFactory->createWithMembers([self::DEFAULT_ZONE_CODE]);
        $zone->setCode(self::DEFAULT_ZONE_CODE);
        $zone->setName(self::DEFAULT_ZONE_NAME);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);

        return $zone;
    }
}
