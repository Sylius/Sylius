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

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class DefaultUnitedStatesChannelFactory implements DefaultChannelFactoryInterface
{
    public const DEFAULT_CHANNEL_CODE = 'WEB-US';

    public const DEFAULT_COUNTRY_CODE = 'US';

    public const DEFAULT_ZONE_CODE = 'US';

    public const DEFAULT_CURRENCY_CODE = 'USD';

    public const DEFAULT_ZONE_NAME = 'United States';

    public const DEFAULT_CHANNEL_NAME = 'United States';

    public function __construct(
        private RepositoryInterface $channelRepository,
        private RepositoryInterface $countryRepository,
        private RepositoryInterface $currencyRepository,
        private RepositoryInterface $localeRepository,
        private RepositoryInterface $zoneRepository,
        private ChannelFactoryInterface $channelFactory,
        private FactoryInterface $countryFactory,
        private FactoryInterface $currencyFactory,
        private FactoryInterface $localeFactory,
        private ZoneFactoryInterface $zoneFactory,
        private string $defaultLocaleCode,
    ) {
    }

    public function create(?string $code = null, ?string $name = null, ?string $currencyCode = null, ?string $localeCode = null): array
    {
        $currency = $this->provideCurrency($currencyCode);
        $locale = $this->provideLocale();

        $channel = $this->createChannel($code ?? self::DEFAULT_CHANNEL_CODE, $name ?? self::DEFAULT_CHANNEL_NAME);
        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);
        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setHostname('us.store.com');

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

        /** @var CurrencyInterface|null $currency */
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
        /** @var LocaleInterface|null $locale */
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
