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
use Behat\Mink\Element\NodeElement;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

class AddressingContext extends DefaultContext
{
    /**
     * @Given /^there are following countries:$/
     * @Given /^the following countries exist:$/
     */
    public function thereAreCountries(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $provinces = array_key_exists('provinces', $data) ? explode(',', $data['provinces']) : array();

            $enabled = isset($data['enabled']) ? 'no' !== $data['enabled'] : true;

            $this->thereisCountry($data['name'], $enabled, $provinces, false);
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
    public function thereIsCountry($name, $enabled = true, $provinces = null, $flush = true)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        /* @var $country CountryInterface */
        if (null === $country = $this->getRepository('country')->findOneBy(array('isoName' => $isoName))) {
            $country = $this->getRepository('country')->createNew();
            $country->setIsoName(trim($isoName));
            $country->setEnabled($enabled);

            $this->addProvincesToCountry($country, $provinces);

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
                explode(',', $data['members']),
                $scope,
                false
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

        /* @var $zone ZoneInterface */
        $zone = $repository->createNew();
        $zone->setName($name);
        $zone->setType($type);
        $zone->setScope($scope);

        foreach ($members as $memberName) {
            $member = $this->getService('sylius.repository.zone_member_'.$type)->createNew();
            if (ZoneInterface::TYPE_ZONE === $type) {
                $zoneable = $repository->findOneBy(array('name' => $memberName));
            } else {
                $zoneable = call_user_func(array($this, 'thereIs'.ucfirst($type)), $memberName);
            }

            call_user_func(array($member, 'set'.ucfirst($type)), $zoneable);

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
     * @Given /^there is province "([^"]*)"$/
     */
    public function thereIsProvince($name)
    {
        /* @var $province ProvinceInterface */
        $province = $this->getRepository('province')->createNew();
        $province->setName($name);

        $this->getEntityManager()->persist($province);

        return $province;
    }

    /**
     * @When /^store owner set country "([^"]*)" as disabled$/
     */
    public function storeOwnerSetCountryAsDisabled($name)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        /** @var CountryInterface $country */
        $country = $this->getRepository("country")->findOneBy(array('isoName' => $isoName));
        $country->setEnabled(false);

        $manager = $this->getEntityManager();
        $manager->persist($country);
        $manager->flush();
    }

    /**
     * @param CountryInterface $country
     * @param TableNode|array $provinces
     */
    private function addProvincesToCountry($country, $provinces)
    {
        if (null !== $provinces) {
            $provinces = $provinces instanceof TableNode ? $provinces->getHash() : $provinces;
            foreach ($provinces as $provinceName) {
                $country->addProvince($this->thereisProvince($provinceName));
            }
        }
    }
}
