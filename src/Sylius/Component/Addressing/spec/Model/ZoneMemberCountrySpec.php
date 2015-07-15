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
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMemberCountry');
    }

    public function it_implements_Sylius_zone_member_interface()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\ZoneMember');
        $this->shouldImplement('Sylius\Component\Addressing\Model\ZoneMemberInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    public function it_does_not_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    public function its_country_is_mutable(CountryInterface $country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }

    public function it_returns_country_name(CountryInterface $country)
    {
        $country->getName()->willReturn('Serbia');
        $this->setCountry($country);

        $this->getName()->shouldReturn('Serbia');
    }

    public function it_has_fluent_interface(CountryInterface $country, ZoneInterface $zone)
    {
        $this->setCountry($country)->shouldReturn($this);
        $this->setBelongsTo($zone)->shouldReturn($this);
    }
}
