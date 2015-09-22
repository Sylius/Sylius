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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ZoneMemberCountrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMemberCountry');
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

    function it_has_no_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    function it_does_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    function its_country_is_mutable(CountryInterface $country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }

    function it_returns_country_name(CountryInterface $country)
    {
        $country->getName()->willReturn('Serbia');
        $this->setCountry($country);

        $this->getName()->shouldReturn('Serbia');
    }
}
