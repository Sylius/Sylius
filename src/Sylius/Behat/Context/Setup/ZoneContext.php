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
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ZoneContext implements Context
{
    /**
     * @var array
     */
    private $euMembers = [
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
        'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL',
        'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB',
    ];

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ZoneRepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ZoneFactoryInterface
     */
    private $zoneFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneMemberFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ZoneRepositoryInterface $zoneRepository
     * @param SettingsManagerInterface $settingsManager
     * @param ObjectManager $objectManager
     * @param ZoneFactoryInterface $zoneFactory
     * @param FactoryInterface $zoneMemberFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ZoneRepositoryInterface $zoneRepository,
        SettingsManagerInterface $settingsManager,
        ObjectManager $objectManager,
        ZoneFactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->zoneRepository = $zoneRepository;
        $this->settingsManager = $settingsManager;
        $this->objectManager = $objectManager;
        $this->zoneFactory = $zoneFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
    }

    /**
     * @Given /^there is a zone "EU" containing all members of the European Union$/
     */
    public function thereIsAZoneEUContainingAllMembersOfEuropeanUnion()
    {
        $zone = $this->zoneFactory->createWithMembers($this->euMembers);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->setCode('EU');
        $zone->setName('European Union');

        $this->zoneRepository->add($zone);
        $this->sharedStorage->set('zone', $zone);
    }

    /**
     * @Given /^there is a zone "The Rest of the World" containing all other countries$/
     */
    public function thereIsAZoneTheRestOfTheWorldContainingAllOtherCountries()
    {
        $restOfWorldCountries = array_diff(
            array_keys(Intl::getRegionBundle()->getCountryNames('en')),
            array_merge($this->euMembers, ['US'])
        );

        $zone = $this->zoneFactory->createWithMembers($restOfWorldCountries);
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->setCode('RoW');
        $zone->setName('The Rest of the World');

        $this->zoneRepository->add($zone);
    }

    /**
     * @Given default tax zone is :zone
     */
    public function defaultTaxZoneIs(ZoneInterface $zone)
    {
        $settings = $this->settingsManager->load('sylius_taxation');
        $settings->set('default_tax_zone', $zone);
        $this->settingsManager->save($settings);
    }

    /**
     * @Given the store does not have any zones defined
     */
    public function theStoreDoesNotHaveAnyZonesDefined()
    {
        $zones = $this->zoneRepository->findAll();

        foreach ($zones as $zone) {
            $this->zoneRepository->remove($zone);
        }
    }

    /**
     * @Given the store has a zone :zoneName with code :code
     * @Given the store also has a zone :zoneName with code :code
     */
    public function theStoreHasAZoneWithCode($zoneName, $code)
    {
        $zone = $this->zoneFactory->createTyped(ZoneInterface::TYPE_ZONE);
        $zone->setCode($code);
        $zone->setName($zoneName);

        $this->sharedStorage->set('zone', $zone);
        $this->zoneRepository->add($zone);
    }

    /**
     * @Given /^(it)(?:| also) has the ("([^"]+)" country) member$/
     * @Given /^(this zone)(?:| also) has the ("([^"]+)" country) member$/
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
     * @Given /^(it) has the ("([^"]+)" province) member$/
     * @Given /^(it) also has the ("([^"]+)" province) member$/
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
     * @Given /^(it) has the (zone named "([^"]+)")$/
     * @Given /^(it) also has the (zone named "([^"]+)")$/
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
