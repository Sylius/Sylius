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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class DefaultFranceChannelFactory implements DefaultChannelFactoryInterface
{
    const DEFAULT_CHANNEL_CODE = 'WEB-FR';
    const DEFAULT_COUNTRY_CODE = 'FR';
    const DEFAULT_CURRENCY_CODE = 'EUR';
    const DEFAULT_ZONE_CODE = 'FR';
    const DEFAULT_ZONE_NAME = 'France';

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
    private $zoneMemberFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneFactory;

    /**
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $currencyRepository
     * @param RepositoryInterface $zoneMemberRepository
     * @param RepositoryInterface $zoneRepository
     * @param ChannelFactoryInterface $channelFactory
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $currencyFactory
     * @param FactoryInterface $zoneFactory
     * @param FactoryInterface $zoneMemberFactory
     */
    public function __construct(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->currencyRepository = $currencyRepository;
        $this->zoneMemberRepository = $zoneMemberRepository;
        $this->zoneRepository = $zoneRepository;
        $this->channelFactory = $channelFactory;
        $this->countryFactory = $countryFactory;
        $this->currencyFactory = $currencyFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->zoneFactory = $zoneFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $currency = $this->createCurrency();

        $channel = $this->createChannel();
        $channel->setDefaultCurrency($currency);

        $defaultData['channel'] = $channel;
        $defaultData['country'] = $this->createCountry();
        $defaultData['currency'] = $currency;
        $defaultData['zone_member'] = $this->createZoneMember();
        $defaultData['zone'] = $this->createZone($defaultData['zone_member']);

        $this->currencyRepository->add($currency);
        $this->channelRepository->add($channel);
        $this->countryRepository->add($defaultData['country']);
        $this->zoneRepository->add($defaultData['zone']);
        $this->zoneMemberRepository->add($defaultData['zone_member']);

        return $defaultData;
    }

    /**
     * @return ChannelInterface
     */
    private function createChannel()
    {
        $channel = $this->channelFactory->createNamed('France');
        $channel->setCode(self::DEFAULT_CHANNEL_CODE);

        return $channel;
    }

    /**
     * @return CountryInterface
     */
    private function createCountry()
    {
        $country = $this->countryFactory->createNew();
        $country->setCode(self::DEFAULT_COUNTRY_CODE);

        return $country;
    }

    /**
     * @return CurrencyInterface
     */
    private function createCurrency()
    {
        $currency = $this->currencyFactory->createNew();
        $currency->setCode(self::DEFAULT_CURRENCY_CODE);
        $currency->setExchangeRate(1.00);
        $currency->setBase(true);

        return $currency;
    }

    /**
     * @return ZoneMemberInterface
     */
    private function createZoneMember()
    {
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
        $zone = $this->zoneFactory->createNew();
        $zone->setCode(self::DEFAULT_ZONE_CODE);
        $zone->setName(self::DEFAULT_ZONE_NAME);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->addMember($zoneMember);

        return $zone;
    }
}
