<?php

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Province zone member model spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberProvince extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince');
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

    function it_should_not_have_province_by_default()
    {
        $this->getProvince()->shouldReturn(null);
    }

    function it_should_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function its_province_should_be_mutable($province)
    {
        $this->setProvince($province);
        $this->getProvince()->shouldReturn($province);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_should_return_province_name($province)
    {
        $name = 'New York';
        $province->getName()->willReturn($name);

        $this->setProvince($province);

        $this->getName()->shouldReturn($name);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $zone
     */
    function it_should_have_fluid_interface($province, $zone)
    {
        $this->setProvince($province)->shouldReturn($this);
        $this->setBelongsTo($zone)->shouldReturn($this);
    }
}
