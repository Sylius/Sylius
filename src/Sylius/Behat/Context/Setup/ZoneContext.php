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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ZoneContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

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
     * @param RepositoryInterface $zoneRepository
     * @param ObjectManager $objectManager
     * @param ZoneFactoryInterface $zoneFactory
     * @param FactoryInterface $zoneMemberFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $zoneRepository,
        ObjectManager $objectManager,
        ZoneFactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->zoneRepository = $zoneRepository;
        $this->objectManager = $objectManager;
        $this->zoneFactory = $zoneFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
    }

    /**
     * @Given /^there is a zone "The Rest of the World" containing all other countries$/
     */
    public function thereIsAZoneTheRestOfTheWorldContainingAllOtherCountries()
    {
        $restOfWorldCountries = Intl::getRegionBundle()->getCountryNames('en');
        unset($restOfWorldCountries['US']);

        $zone = $this->zoneFactory->createWithMembers(array_keys($restOfWorldCountries));
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
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setDefaultTaxZone($zone);

        $this->objectManager->flush();
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
        $this->saveZone($this->setUpZone($zoneName, $code, Scope::ALL), 'zone');
    }

    /**
     * @Given the store has a :scope zone :zoneName with code :code
     */
    public function theStoreHasAScopedZoneWithCode($scope, $zoneName, $code)
    {
        $this->saveZone($this->setUpZone($zoneName, $code, $scope), $scope . '_zone');
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
     * @Given /^(it) has the ("[^"]+" province) member$/
     * @Given /^(it) also has the ("[^"]+" province) member$/
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

    /**
     * @param string $zoneName
     * @param string $code
     * @param string $scope
     *
     * @return ZoneInterface
     */
    private function setUpZone($zoneName, $code, $scope)
    {
        $zone = $this->zoneFactory->createTyped(ZoneInterface::TYPE_ZONE);
        $zone->setCode($code);
        $zone->setName($zoneName);
        $zone->setScope($scope);

        return $zone;
    }

    /**
     * @param ZoneInterface $zone
     * @param string $key
     */
    private function saveZone($zone, $key)
    {
        $this->sharedStorage->set($key, $zone);
        $this->zoneRepository->add($zone);
    }
}
