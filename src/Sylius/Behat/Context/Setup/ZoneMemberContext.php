<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ZoneMemberContext implements Context
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $zoneMemberFactory;

    /**
     * @param ObjectManager $objectManager
     * @param FactoryInterface $zoneMemberFactory
     */
    public function __construct(
        ObjectManager $objectManager,
        FactoryInterface $zoneMemberFactory
    ) {
        $this->objectManager = $objectManager;
        $this->zoneMemberFactory = $zoneMemberFactory;
    }

    /**
     * @Given /^(it) has the ("([^"]*)" country) member$/
     * @Given /^(this zone) has the ("([^"]*)" country) member$/
     * @Given /^(it) also has the ("([^"]*)" country) member$/
     * @Given /^(this zone) also has the ("([^"]*)" country) member$/
     */
    public function itHasTheCountryMemberAndTheCountryMember(
        ZoneInterface $zone,
        CountryInterface $country
    ) {
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->addMember($this->createZoneMember($country));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) has the ("([^"]*)" province) member$/
     * @Given /^(it) also has the ("([^"]*)" province) member$/
     */
    public function itHasTheProvinceMemberAndTheProvinceMember(
        ZoneInterface $zone,
        ProvinceInterface $province
    ) {
        $zone->setType(ZoneInterface::TYPE_PROVINCE);
        $zone->addMember($this->createZoneMember($province));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) has the (zone named "([^"]*)")$/
     * @Given /^(it) also has the (zone named "([^"]*)")$/
     */
    public function itHasTheZoneMemberAndTheZoneMember(
        ZoneInterface $parentZone,
        ZoneInterface $childZone
    ) {
        $parentZone->setType(ZoneInterface::TYPE_ZONE);
        $parentZone->addMember($this->createZoneMember($childZone));

        $this->objectManager->flush();
    }

    /**
     * @param CodeAwareInterface $zoneMember
     *
     * @return ZoneMemberInterface
     */
    private function createZoneMember(CodeAwareInterface $zoneMember)
    {
        $code = $zoneMember->getCode();
        /** @var ZoneMemberInterface $zoneMember */
        $zoneMember = $this->zoneMemberFactory->createNew();
        $zoneMember->setCode($code);

        return $zoneMember;
    }
}
