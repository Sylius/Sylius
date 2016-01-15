<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

class AddressingContext extends DefaultContext
{
    /**
     * @Given /^there are following countries:$/
     * @Given /^the following countries exist:$/
     */
    public function thereAreCountries(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $administrativeAreas = array_key_exists('administrative areas', $data) ? explode(',', $data['administrative areas']) : array();

            $enabled = isset($data['enabled']) ? 'no' !== $data['enabled'] : true;

            $this->thereisCountry($data['name'], $enabled, $administrativeAreas, false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is a disabled country "([^""]*)"$/
     */
    public function thereIsDisabledCountry($name)
    {
        $this->thereIsCountry($name, false);
    }

    /**
     * @Given /^I created country "([^""]*)"$/
     * @Given /^there is country "([^""]*)"$/
     * @Given /^there is an enabled country "([^""]*)"$/
     */
    public function thereIsCountry($name, $enabled = true, $administrativeAreas = null, $flush = true)
    {
        $countryCode = $this->getCountryCodeByEnglishCountryName($name);

        /** @var $country CountryInterface */
        if (null === $country = $this->getRepository('country')->findOneBy(array('code' => $countryCode))) {
            $country = $this->getFactory('country')->createNew();
            $country->setCode(trim($countryCode));
            $country->setEnabled($enabled);

            $this->addAdministrativeAreaToCountry($country, $administrativeAreas);

            $manager = $this->getEntityManager();
            $manager->persist($country);
            if ($flush) {
                $manager->flush();
            }
        }

        return $country;
    }

    /**
     * @Given /^the following zones are defined:$/
     * @Given /^there are following zones:$/
     */
    public function thereAreFollowingZones(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $scope = null;
            if (!empty($data['scope'])) {
                $scope = $data['scope'];
            }

            $this->thereIsZone(
                $data['name'],
                $data['type'],
                explode(', ', $data['members']),
                $scope
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created zone "([^"]*)"$/
     * @Given /^there is zone "([^"]*)"$/
     */
    public function thereIsZone($name, $type = ZoneInterface::TYPE_COUNTRY, array $members = array(), $scope = null, $flush = true)
    {
        $repository = $this->getRepository('zone');

        /** @var $zone ZoneInterface */
        $zone = $this->getFactory('zone')->createNew();
        $zone->setName($name);
        $zone->setCode($name);
        $zone->setType($type);
        $zone->setScope($scope);

        if (false !== strpos($type, '-')) {
            $type = implode(explode('-', $type));
        }

        foreach ($members as $memberName) {
            if (ZoneInterface::TYPE_ZONE === $type) {
                $zoneable = $repository->findOneBy(array('name' => $memberName));
            } else {
                $zoneable = call_user_func(array($this, 'thereIs'.$type), $memberName);
            }

            /** @var ZoneMemberInterface $member */
            $member = $this->getFactory('zone_member')->createNew();
            $member->setCode($zoneable->getCode());
            $zone->addMember($member);
        }

        $manager = $this->getEntityManager();
        $manager->persist($zone);
        if ($flush) {
            $manager->flush();
        }

        return $zone;
    }

    /**
     * @Given /^there is administrative area "([^"]*)"$/
     */
    public function thereIsAdministrativeArea($name)
    {
        /** @var $administrativeArea AdministrativeAreaInterface */
        $administrativeArea = $this->getFactory('administrative_area')->createNew();
        $administrativeArea->setCode($name);
        $administrativeArea->setName($name);

        $this->getEntityManager()->persist($administrativeArea);

        return $administrativeArea;
    }

    /**
     * @When /^store owner set country "([^"]*)" as disabled$/
     */
    public function storeOwnerSetCountryAsDisabled($name)
    {
        $countryCode = $this->getCountryCodeByEnglishCountryName($name);

        /** @var CountryInterface $country */
        $country = $this->getRepository("country")->findOneBy(array('code' => $countryCode));
        $country->setEnabled(false);

        $manager = $this->getEntityManager();
        $manager->persist($country);
        $manager->flush();
    }

    /**
     * @param CountryInterface $country
     * @param TableNode|array $administrativeAreas
     */
    private function addAdministrativeAreaToCountry($country, $administrativeAreas)
    {
        if (null !== $administrativeAreas) {
            $administrativeAreas = $administrativeAreas instanceof TableNode ? $administrativeAreas->getHash() : $administrativeAreas;
            foreach ($administrativeAreas as $administrativeAreaName) {
                $country->addAdministrativeArea($this->thereIsAdministrativeArea($administrativeAreaName));
            }
        }
    }
}
