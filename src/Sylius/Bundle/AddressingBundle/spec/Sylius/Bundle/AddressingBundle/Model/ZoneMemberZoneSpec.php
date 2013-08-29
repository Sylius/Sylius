<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberZoneSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMemberZone');
    }

    function it_implements_Sylius_zone_member_interface()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMember');
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface');
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

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function its_zone_is_mutable($zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function it_returns_zone_name($zone)
    {
        $zone->getName()->willReturn('USA');
        $this->setZone($zone);

        $this->getName()->shouldReturn('USA');
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $belongsTo
     */
    function it_has_fluent_interface($zone, $belongsTo)
    {
        $this->setZone($zone)->shouldReturn($this);
        $this->setBelongsTo($belongsTo)->shouldReturn($this);
    }
}
