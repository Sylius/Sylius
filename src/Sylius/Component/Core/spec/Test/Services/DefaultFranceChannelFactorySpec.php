<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class DefaultFranceChannelFactorySpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $channelRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $zoneMemberFactory,
        FactoryInterface $zoneFactory
    ) {
        $this->beConstructedWith(
            $channelRepository,
            $zoneMemberRepository,
            $zoneRepository,
            $channelFactory,
            $zoneMemberFactory,
            $zoneFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\DefaultFranceChannelFactory');
    }

    function it_is_default_store_data()
    {
        $this->shouldImplement('Sylius\Component\Core\Test\Services\DefaultStoreDataInterface');
    }

    function it_creates_france_country_and_zone(
        $channelRepository,
        $zoneMemberRepository,
        $zoneRepository,
        $channelFactory,
        $zoneMemberFactory,
        $zoneFactory,
        ZoneMemberInterface $zoneMember,
        ZoneInterface $zone,
        ChannelInterface $channel
    ) {
        $channel->getName()->willReturn('France');
        $channelFactory->createNamed('France')->willReturn($channel);

        $zoneMemberFactory->createNew()->willReturn($zoneMember);
        $zoneFactory->createNew()->willReturn($zone);

        $channel->setCode('WEB-FR')->shouldBeCalled();

        $zoneMember->setCode('FR')->shouldBeCalled();
        $zone->setCode('FR')->shouldBeCalled();
        $zone->setName('France')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->addMember($zoneMember)->shouldBeCalled();

        $channelRepository->add($channel)->shouldBeCalled();
        $zoneRepository->add($zone)->shouldBeCalled();
        $zoneMemberRepository->add($zoneMember)->shouldBeCalled();

        $this->create();
    }
}
