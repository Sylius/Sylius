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
    function it_should_return_null_if_there_are_no_zones($repository, $address)
    {
        $repository->findAll()->willReturn(array());
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
        $address->getProvince()->shouldBeCalled()->willReturn($province);
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

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
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

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
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $subZone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $subZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->shouldBeCalled()->willReturn($subZone);
        $rootZone->getMembers()->shouldBeCalled()->willReturn(array($memberZone));
        $rootZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
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
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zoneProvince->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));
        $zoneProvince->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->shouldNotBeCalled()->willReturn(array());
        $zoneCountry->getType()->shouldNotBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);

        $this->match($address)->shouldReturn($zoneProvince);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface   $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface   $address
     * @param Sylius\Bundle\AddressingBundle\Entity\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $zone
     */
    function it_should_match_all_addresses($repository, $country, $address, $memberCountry, $zone)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zone));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->matchAll($address)->shouldReturn(array($zone));
    }
}
