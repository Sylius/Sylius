<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ZoneMemberZoneSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMemberZone');
    }

    function it_implements_Sylius_zone_member_interface()
    {
        $this->shouldHaveType(ZoneMember::class);
        $this->shouldImplement(ZoneMemberInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_zone_by_default()
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_does_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    function its_zone_is_mutable(ZoneInterface $zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    function it_returns_zone_name(ZoneInterface $zone)
    {
        $zone->getName()->willReturn('USA');
        $this->setZone($zone);

        $this->getName()->shouldReturn('USA');
    }
}
