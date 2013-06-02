<?php

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Country zone member model spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberCountry extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry');
    }

    function it_is_Sylius_zone_member()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\ZoneMember');
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    function it_should_not_belong_to_any_zone_by_default()
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
        $name = 'Serbia';
        $country->getName()->willReturn($name);

        $this->setCountry($country);

        $this->getName()->shouldReturn($name);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface    $zone
     */
    function it_should_have_fluid_interface($country, $zone)
    {
        $this->setCountry($country)->shouldReturn($this);
        $this->setBelongsTo($zone)->shouldReturn($this);
    }
}
