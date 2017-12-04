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

namespace spec\Sylius\Component\Addressing\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Factory\ZoneFactory;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ZoneFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, FactoryInterface $zoneMemberFactory): void
    {
        $this->beConstructedWith($factory, $zoneMemberFactory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ZoneFactory::class);
    }

    function it_implements_factory_interface(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_zone_factory_interface(): void
    {
        $this->shouldImplement(ZoneFactoryInterface::class);
    }

    function it_creates_zone_with_type(FactoryInterface $factory, ZoneInterface $zone): void
    {
        $factory->createNew()->willReturn($zone);
        $zone->setType('country')->shouldBeCalled();

        $this->createTyped('country')->shouldReturn($zone);
    }

    function it_creates_zone_with_members(
        FactoryInterface $factory,
        FactoryInterface $zoneMemberFactory,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember1,
        ZoneMemberInterface $zoneMember2
    ): void {
        $factory->createNew()->willReturn($zone);
        $zoneMemberFactory->createNew()->willReturn($zoneMember1, $zoneMember2);

        $zoneMember1->setCode('GB')->shouldBeCalled();
        $zoneMember2->setCode('PL')->shouldBeCalled();

        $zone->addMember($zoneMember1)->shouldBeCalled();
        $zone->addMember($zoneMember2)->shouldBeCalled();

        $this->createWithMembers(['GB', 'PL'])->shouldReturn($zone);
    }
}
