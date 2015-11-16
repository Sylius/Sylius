<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Intl\Intl;

/**
 * Default zone fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadZonesData extends DataFixture
{
    private $euCountries = array(
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
        'FR', 'IT', 'CY', 'LV', 'LV', 'LT', 'LU', 'HU', 'MT',
        'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE',
        'GB',
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $restOfWorldCountries = array_diff(
            array_keys(Intl::getRegionBundle()->getCountryNames($this->container->getParameter('sylius.locale'))),
            $this->euCountries + array('US')
        );

        $zoneManager = $this->getZoneManager();

        $zoneManager->persist($eu = $this->createZone('EU', ZoneInterface::TYPE_COUNTRY, $this->euCountries));
        $zoneManager->persist($this->createZone('USA', ZoneInterface::TYPE_COUNTRY, array('US')));
        $zoneManager->persist($this->createZone('EU + USA', ZoneInterface::TYPE_ZONE, array('EU', 'USA')));
        $zoneManager->persist($this->createZone('Rest of World', ZoneInterface::TYPE_COUNTRY, $restOfWorldCountries));

        $settingsManager = $this->get('sylius.settings.manager');
        $settings = $settingsManager->loadSettings('sylius_taxation');
        $settings->set('default_tax_zone', $eu);

        $settingsManager->saveSettings('sylius_taxation', $settings);

        $zoneManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
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
    protected function createZone($name, $type, array $members)
    {
        /* @var $zone ZoneInterface */
        $zone = $this->getZoneFactory()->createNew();
        $zone->setName($name);
        $zone->setType($type);

        foreach ($members as $id) {
            /* @var $zoneMember ZoneMemberInterface */
            $zoneMember = $this->getZoneMemberFactory($type)->createNew();

            if ($this->hasReference('Sylius.'.ucfirst($type).'.'.$id)) {
                $zoneMember->{'set'.ucfirst($type)}($this->getReference('Sylius.'.ucfirst($type).'.'.$id));
            }

            $zone->addMember($zoneMember);
        }

        $this->setReference('Sylius.Zone.'.$name, $zone);

        return $zone;
    }
}
