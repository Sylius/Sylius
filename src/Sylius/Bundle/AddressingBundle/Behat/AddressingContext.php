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
use Symfony\Component\Locale\Locale;

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
            $this->thereisCountry($data['name'], $provinces, false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created country "([^""]*)"$/
     * @Given /^there is country "([^""]*)"$/
     */
    public function thereIsCountry($name, $provinces = null, $flush = true)
    {
        /* @var $country CountryInterface */
        if (null === $country = $this->getRepository('country')->findOneBy(array('name' => $name))) {
            $country = $this->getRepository('country')->createNew();
            $country->setName(trim($name));
            $country->setIsoName(array_search($name, Locale::getDisplayCountries(Locale::getDefault())));

            if (null !== $provinces) {
                $provinces = $provinces instanceof TableNode ? $provinces->getHash() : $provinces;
                foreach ($provinces as $provinceName) {
                    $country->addProvince($this->thereisProvince($provinceName));
                }
            }

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
            if (array_key_exists('scope', $data) && strlen($data['scope']) > 0){
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
}
