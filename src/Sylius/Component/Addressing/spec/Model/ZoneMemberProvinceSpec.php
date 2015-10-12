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
use Sylius\Component\Addressing\Model\ProvinceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ZoneMemberProvinceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMemberProvince');
    }

    function it_implements_Sylius_zone_member_interface()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMember');
        $this->shouldImplement('Sylius\Component\Addressing\Model\ZoneMemberInterface');
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

    function its_province_is_mutable(ProvinceInterface $province)
    {
        $this->setProvince($province);
        $this->getProvince()->shouldReturn($province);
    }

    function it_returns_province_name(ProvinceInterface $province)
    {
        $province->getName()->willReturn('Łódzkie');
        $this->setProvince($province);

        $this->getName()->shouldReturn('Łódzkie');
    }
}
