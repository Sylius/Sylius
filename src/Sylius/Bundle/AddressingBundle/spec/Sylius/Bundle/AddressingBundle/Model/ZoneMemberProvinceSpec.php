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
class ZoneMemberProvinceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince');
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

    function it_has_no_province_by_default()
    {
        $this->getProvince()->shouldReturn(null);
    }

    function it_does_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function its_province_is_mutable($province)
    {
        $this->setProvince($province);
        $this->getProvince()->shouldReturn($province);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_returns_province_name($province)
    {
        $province->getName()->willReturn('Łódzkie');
        $this->setProvince($province);

        $this->getName()->shouldReturn('Łódzkie');
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $zone
     */
    function it_has_fluent_interface($province, $zone)
    {
        $this->setProvince($province)->shouldReturn($this);
        $this->setBelongsTo($zone)->shouldReturn($this);
    }
}
