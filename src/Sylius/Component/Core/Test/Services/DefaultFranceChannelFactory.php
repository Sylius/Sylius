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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class DefaultFranceChannelFactory implements DefaultStoreDataInterface
{
    /**
     * @var array
     */
    private $defaultCountries = ['FR', 'GB', 'US', 'CN', 'AU'];

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
    private $zoneMemberFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneFactory;

    /**
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $zoneMemberRepository
     * @param RepositoryInterface $zoneRepository
     * @param FactoryInterface $channelFactory
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $zoneMemberFactory
     * @param FactoryInterface $zoneFactory
     */
    public function __construct(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $zoneMemberFactory,
        FactoryInterface $zoneFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->zoneMemberRepository = $zoneMemberRepository;
        $this->zoneRepository = $zoneRepository;
        $this->channelFactory = $channelFactory;
        $this->countryFactory = $countryFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->zoneFactory = $zoneFactory;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $defaultData['channel'] = $this->createChannel();
        $defaultData['zone_member'] = $this->createZoneMember();
        $defaultData['zone'] = $this->createZone($defaultData['zone_member']);

        $this->channelRepository->add($defaultData['channel']);

        foreach ($this->defaultCountries as $country) {
            $this->countryRepository->add($this->createCountry($country));
        }
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
        $channel->setCode('WEB-FR');

        return $channel;
    }

    /**
     * @param string $code
     *
     * @return CountryInterface
     */
    private function createCountry($code)
    {
        $country = $this->countryFactory->createNew();
        $country->setCode($code);

        return $country;
    }

    /**
     * @return ZoneMemberInterface
     */
    private function createZoneMember()
    {
        $zoneMember = $this->zoneMemberFactory->createNew();
        $zoneMember->setCode('FR');

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
        $zone->setCode('FR');
        $zone->setName('France');
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->addMember($zoneMember);

        return $zone;
    }
}
