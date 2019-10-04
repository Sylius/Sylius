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

final class DefaultUnitedStatesChannelFactory implements DefaultChannelFactoryInterface
{
    public const DEFAULT_CHANNEL_CODE = 'WEB-US';

    public const DEFAULT_COUNTRY_CODE = 'US';

    public const DEFAULT_ZONE_CODE = 'US';

    public const DEFAULT_CURRENCY_CODE = 'USD';

    public const DEFAULT_ZONE_NAME = 'United States';

    public const DEFAULT_CHANNEL_NAME = 'United States';

    /** @var RepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var ChannelFactoryInterface */
    private $channelFactory;

    /** @var FactoryInterface */
    private $countryFactory;

    /** @var FactoryInterface */
    private $currencyFactory;

    /** @var FactoryInterface */
    private $localeFactory;

    /** @var ZoneFactoryInterface */
    private $zoneFactory;

    /** @var string */
    private $defaultLocaleCode;

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
        string $defaultLocaleCode
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
    public function create(?string $code = null, ?string $name = null, ?string $currencyCode = null): array
    {
        $currency = $this->provideCurrency($currencyCode);
        $locale = $this->provideLocale();

        $channel = $this->createChannel($code ?? self::DEFAULT_CHANNEL_CODE, $name ?? self::DEFAULT_CHANNEL_NAME);
        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);
        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);
        $channel->setTaxCalculationStrategy('order_items_based');

        $defaultData = [
            'channel' => $channel,
            'country' => $this->createCountry(),
            'currency' => $currency,
            'locale' => $locale,
            'zone' => $this->createZone(),
        ];

        $this->channelRepository->add($channel);
        $this->countryRepository->add($defaultData['country']);
        $this->zoneRepository->add($defaultData['zone']);

        return $defaultData;
    }

    private function createChannel(string $code, string $name): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($name);
        $channel->setCode($code);

        return $channel;
    }

    private function createCountry(): CountryInterface
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode(self::DEFAULT_COUNTRY_CODE);

        return $country;
    }

    private function provideCurrency(?string $currencyCode = null): CurrencyInterface
    {
        $currencyCode = $currencyCode ?? self::DEFAULT_CURRENCY_CODE;

        /** @var CurrencyInterface $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);

        if (null === $currency) {
            /** @var CurrencyInterface $currency */
            $currency = $this->currencyFactory->createNew();
            $currency->setCode($currencyCode);

            $this->currencyRepository->add($currency);
        }

        return $currency;
    }

    private function provideLocale(): LocaleInterface
    {
        /** @var LocaleInterface $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $this->defaultLocaleCode]);

        if (null === $locale) {
            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();
            $locale->setCode($this->defaultLocaleCode);

            $this->localeRepository->add($locale);
        }

        return $locale;
    }

    private function createZone(): ZoneInterface
    {
        /** @var ZoneInterface $zone */
        $zone = $this->zoneFactory->createWithMembers([self::DEFAULT_ZONE_CODE]);
        $zone->setCode(self::DEFAULT_ZONE_CODE);
        $zone->setName(self::DEFAULT_ZONE_NAME);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);

        return $zone;
    }
}
