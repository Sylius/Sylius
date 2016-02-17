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
use Sylius\Component\Addressing\Model\ProvinceInterface;
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
            $provinces = array_key_exists('provinces', $data) ? explode(',', $data['provinces']) : [];

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
        $countryCode = $this->getCountryCodeByEnglishCountryName($name);

        /* @var $country CountryInterface */
        if (null === $country = $this->getRepository('country')->findOneBy(['code' => $countryCode])) {
            $country = $this->getFactory('country')->createNew();
            $country->setCode(trim($countryCode));
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
    public function thereIsZone($name, $type = ZoneInterface::TYPE_COUNTRY, array $members = [], $scope = null, $flush = true)
    {
        $repository = $this->getRepository('zone');

        /* @var $zone ZoneInterface */
        $zone = $this->getFactory('zone')->createNew();
        $zone->setName($name);
        $zone->setCode($name);
        $zone->setType($type);
        $zone->setScope($scope);

        foreach ($members as $memberName) {
            if (ZoneInterface::TYPE_ZONE === $type) {
                $zoneable = $repository->findOneBy(['name' => $memberName]);
            } else {
                $zoneable = call_user_func([$this, 'thereIs'.ucfirst($type)], $memberName);
            }

            /* @var ZoneMemberInterface $member */
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
     * @Given /^there is province "([^"]*)"$/
     */
    public function thereIsProvince($name, $countryCode = null)
    {
        /* @var $province ProvinceInterface */
        $province = $this->getFactory('province')->createNew();
        $province->setName($name);
        $province->setCode(sprintf('%s-%s', $countryCode, $name));

        $this->getEntityManager()->persist($province);

        return $province;
    }

    /**
     * @When /^store owner set country "([^"]*)" as disabled$/
     */
    public function storeOwnerSetCountryAsDisabled($name)
    {
        $countryCode = $this->getCountryCodeByEnglishCountryName($name);

        /** @var CountryInterface $country */
        $country = $this->getRepository('country')->findOneBy(['code' => $countryCode]);
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
                $country->addProvince($this->thereisProvince($provinceName, $country->getCode()));
            }
        }
    }
}
