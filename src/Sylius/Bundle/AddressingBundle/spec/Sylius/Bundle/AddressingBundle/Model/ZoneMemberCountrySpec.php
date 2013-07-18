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
class ZoneMemberCountrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry');
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

    function it_has_no_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    function it_does_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function its_country_is_mutable($country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function it_returns_country_name($country)
    {
        $country->getName()->willReturn('Serbia');
        $this->setCountry($country);

        $this->getName()->shouldReturn('Serbia');
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface    $zone
     */
    function it_has_fluent_interface($country, $zone)
    {
        $this->setCountry($country)->shouldReturn($this);
        $this->setBelongsTo($zone)->shouldReturn($this);
    }
}
