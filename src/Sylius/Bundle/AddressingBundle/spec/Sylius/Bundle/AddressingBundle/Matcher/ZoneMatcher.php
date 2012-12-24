<?php

namespace spec\Sylius\Bundle\AddressingBundle\Matcher;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;

/**
 * Zone matcher spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMatcher extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository $repository
     */
    function let($repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcher');
    }

    function it_should_be_Sylius_zone_matcher()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcherInterface');
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_should_return_null_if_there_are_no_zones($address)
    {
        $this->match($address)->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface   $province
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface    $address
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberProvince $memberProvince
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface       $zone
     */
    function it_should_match_address_by_province($repository, $province, $address, $memberProvince, $zone)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zone));
        $province->getId()->shouldBeCalled()->willReturn(7);
        $address->getProvince()->shouldBeCalled()->willReturn($province);
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));

        $this->match($address)->shouldReturn($zone);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface   $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface   $address
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $zone
     */
    function it_should_match_address_by_country($repository, $country, $address, $memberCountry, $zone)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zone));
        $country->getId()->shouldBeCalled()->willReturn(7);
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));

        $this->match($address)->shouldReturn($zone);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface   $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface   $address
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $subZone
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberZone    $memberZone
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $rootZone
     */
    function it_should_match_address_for_nested_zones($repository, $country, $address, $memberCountry, $subZone, $memberZone, $rootZone)
    {
        $country->getId()->shouldBeCalled()->willReturn(7);
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $subZone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $subZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->shouldBeCalled()->willReturn($subZone);
        $rootZone->getMembers()->shouldBeCalled()->willReturn(array($memberZone));
        $rootZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_ZONE);
        $repository->findAll()->shouldBeCalled()->willReturn(array($rootZone));

        $this->match($address)->shouldReturn($rootZone);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface   $province
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface    $address
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberProvince $memberProvince
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface       $zoneCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface       $zoneProvince
     */
    function it_should_match_address_by_zone_priority($repository, $province, $address, $memberProvince, $zoneCountry, $zoneProvince)
    {
        $address->getProvince()->shouldBeCalled()->willReturn($province);
        $province->getId()->shouldBeCalled()->willReturn(7);
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zoneProvince->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));
        $zoneProvince->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->shouldNotBeCalled();
        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry, $zoneProvince));

        $this->match($address)->shouldReturn($zoneProvince);
    }
}
