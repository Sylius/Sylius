<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Matcher;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMatcherSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository $repository
     */
    function let($repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcher');
    }

    function it_is_Sylius_zone_matcher()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcherInterface');
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_returns_null_if_there_are_no_zones($repository, $address)
    {
        $repository->findAll()->willReturn(array());
        $this->match($address)->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface  $province
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface   $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince $memberProvince
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $zone
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
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface  $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $zone
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
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface  $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $subZone
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberZone    $memberZone
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $rootZone
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
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface  $province
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface   $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface   $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry  $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince $memberProvince
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $zoneCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface      $zoneProvince
     */
    function it_should_match_address_from_province_when_many_are_found(
        $repository, $country, $province, $address, $memberCountry, $memberProvince, $zoneCountry, $zoneProvince
    )
    {
        $address->getProvince()->willReturn($province);
        $address->getCountry()->willReturn($country);
        $memberProvince->getProvince()->willReturn($province);
        $memberCountry->getCountry()->willReturn($country);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address)->shouldReturn($zoneProvince);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface  $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry $memberCountry
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface     $zoneCountry
     */
    function it_should_match_all_zones_when_one_zone_for_address_is_defined($repository, $country, $address, $memberCountry, $zoneCountry)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zoneCountry->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->matchAll($address)->shouldReturn(array($zoneCountry));
    }
}
