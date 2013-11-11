<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Intl\Intl;

/**
 * Default zone fixtures.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class LoadZonesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $euCountries = array(
            'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
            'FR', 'IT', 'CY', 'LV', 'LV', 'LT', 'LU', 'HU', 'MT',
            'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE',
            'GB'
        );

        $restOfWorldCountries = array_diff(Intl::getRegionBundle()->getCountryNames(), $euCountries + array('US'));

        $manager->persist($eu = $this->createZone('EU', ZoneInterface::TYPE_COUNTRY, $euCountries));
        $manager->persist($this->createZone('USA', ZoneInterface::TYPE_COUNTRY, array('US')));
        $manager->persist($this->createZone('EU + USA', ZoneInterface::TYPE_ZONE, array('EU', 'USA')));
        $manager->persist($this->createZone('Rest of World', ZoneInterface::TYPE_COUNTRY, $restOfWorldCountries));

        $manager->flush();

        $settingsManager = $this->get('sylius.settings.manager');
        $settings = $settingsManager->loadSettings('taxation');
        $settings->set('default_tax_zone', $eu);
        $settingsManager->saveSettings('taxation', $settings);
    }

    /**
     * Create a new zone instance of given type.
     *
     * @param string $name
     * @param string $type
     * @param array  $members
     *
     * @return ZoneInterface
     */
    private function createZone($name, $type, array $members)
    {
        $zone = $this->getZoneRepository()->createNew();

        $zone->setName($name);
        $zone->setType($type);

        foreach ($members as $id) {
            $zoneMember = $this->getZoneMemberRepository($type)->createNew();

            if ($this->hasReference('Sylius.'.ucfirst($type).'.'.$id)) {
                call_user_func(array(
                    $zoneMember, 'set'.ucfirst($type)),
                    $this->getReference('Sylius.'.ucfirst($type).'.'.$id)
                );
            }

            $zone->addMember($zoneMember);
        }

        $this->setReference('Sylius.Zone.'.$name, $zone);

        return $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
