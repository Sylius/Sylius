<?php

namespace spec\Sylius\Bundle\AddressingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Zone member zone model spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberZone extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Entity\ZoneMemberZone');
    }

    function it_should_be_Sylius_zone_member()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMember');
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_zone_by_default()
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_should_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function its_zone_should_be_mutable($zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function it_should_return_zone_name($zone)
    {
        $name = 'New York';
        $zone->getName()->willReturn($name);

        $this->setZone($zone);

        $this->getName()->shouldReturn($name);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $belongsTo
     */
    function it_should_have_fluid_interface($zone, $belongsTo)
    {
        $this->setZone($zone)->shouldReturn($this);
        $this->setBelongsTo($belongsTo)->shouldReturn($this);
    }
}
