<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Countries;

final class ZoneContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $zoneRepository,
        private ObjectManager $objectManager,
        private ZoneFactoryInterface $zoneFactory,
        private FactoryInterface $zoneMemberFactory,
    ) {
    }

    /**
     * @Given /^there is a zone "The Rest of the World" containing all other countries$/
     */
    public function thereIsAZoneTheRestOfTheWorldContainingAllOtherCountries()
    {
        $restOfWorldCountries = Countries::getNames('en');
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
     * @Given the store has (also) a zone :zoneName
     * @Given the store has a zone :zoneName with code :code
     * @Given the store also has a zone :zoneName with code :code
     */
    public function theStoreHasAZoneWithCode(string $zoneName, ?string $code = null): void
    {
        $this->saveZone($this->createZone($zoneName, $code, Scope::ALL), 'zone');
    }

    /**
     * @Given the store has zones :firstName, :secondName and :thirdName
     */
    public function theStoreHasZones(string ...$names): void
    {
        foreach ($names as $name) {
            $this->theStoreHasAZoneWithCode($name);
        }
    }

    /**
     * @Given the store has a :scope zone :zoneName with code :code
     */
    public function theStoreHasAScopedZoneWithCode($scope, $zoneName, $code)
    {
        $this->saveZone($this->createZone($zoneName, $code, $scope), $scope . '_zone');
    }

    /**
     * @Given /^(it)(?:| also) has the ("([^"]+)" country) member$/
     * @Given /^(this zone)(?:| also) has the ("([^"]+)" country) member$/
     */
    public function itHasTheCountryMemberAndTheCountryMember(
        ZoneInterface $zone,
        CountryInterface $country,
    ) {
        $zone->setType(ZoneInterface::TYPE_COUNTRY);
        $zone->addMember($this->createZoneMember($country));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the "([^"]*)" (?:country|province|zone) member) has been removed from (this zone)$/
     */
    public function theZoneMemberHasBeenRemoved(
        ZoneMemberInterface $zoneMember,
        string $zoneMemberName,
        ZoneInterface $zone,
    ): void {
        $zone->removeMember($zoneMember);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it)(?:| also) has the ("([^"]+)", "([^"]+)" and "([^"]+)" country) members$/
     */
    public function itHasCountryMembers(ZoneInterface $zone, array $countries): void
    {
        $zone->setType(ZoneInterface::TYPE_COUNTRY);

        foreach ($countries as $country) {
            $zone->addMember($this->createZoneMember($country));
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) has the ("[^"]+" province) member$/
     * @Given /^(it) also has the ("[^"]+" province) member$/
     */
    public function itHasTheProvinceMemberAndTheProvinceMember(
        ZoneInterface $zone,
        ProvinceInterface $province,
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
        ZoneInterface $childZone,
    ) {
        $parentZone->setType(ZoneInterface::TYPE_ZONE);
        $parentZone->addMember($this->createZoneMember($childZone));

        $this->objectManager->flush();
    }

    /**
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

    private function createZone(string $name, ?string $code = null, ?string $scope = Scope::ALL): ZoneInterface
    {
        $zone = $this->zoneFactory->createTyped(ZoneInterface::TYPE_ZONE);
        $zone->setCode($code ?? StringInflector::nameToCode($name));
        $zone->setName($name);
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
