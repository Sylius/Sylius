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
    const DEFAULT_CHANNEL_CODE = 'WEB-FR';
    const DEFAULT_ZONE_CODE = 'FR';
    const DEFAULT_ZONE_NAME = 'France';

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

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
    private $zoneMemberFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneFactory;

    /**
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $zoneMemberRepository
     * @param RepositoryInterface $zoneRepository
     * @param FactoryInterface $channelFactory
     * @param FactoryInterface $zoneMemberFactory
     * @param FactoryInterface $zoneFactory
     */
    public function __construct(
        RepositoryInterface $channelRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $channelFactory,
        FactoryInterface $zoneMemberFactory,
        FactoryInterface $zoneFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->zoneMemberRepository = $zoneMemberRepository;
        $this->zoneRepository = $zoneRepository;
        $this->channelFactory = $channelFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->zoneFactory = $zoneFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $defaultData['channel'] = $this->createChannel();
        $defaultData['zone_member'] = $this->createZoneMember();
        $defaultData['zone'] = $this->createZone($defaultData['zone_member']);

        $this->channelRepository->add($defaultData['channel']);
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
