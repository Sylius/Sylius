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
use Symfony\Component\Intl\Intl;

/**
 * Default zone fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadZonesData extends DataFixture
{
    protected $euCountries = [
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
        'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL',
        'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $restOfWorldCountries = array_diff(
            array_keys(Intl::getRegionBundle()->getCountryNames($this->container->getParameter('locale'))),
            array_merge($this->euCountries, ['US'])
        );

        $manager->persist($eu = $this->createZone('EU', 'European Union', ZoneInterface::TYPE_COUNTRY, $this->euCountries));
        $manager->persist($this->createZone('USA', 'United States of America', ZoneInterface::TYPE_COUNTRY, ['US']));
        $manager->persist($this->createZone('EUSA', 'EU + USA', ZoneInterface::TYPE_ZONE, ['EU', 'USA']));
        $manager->persist($this->createZone('RoW', 'Rest of World', ZoneInterface::TYPE_COUNTRY, $restOfWorldCountries));

        $manager->flush();

        $settingsManager = $this->get('sylius.settings.manager');
        $settings = $settingsManager->load('sylius_taxation');
        $settings->set('default_tax_zone', $eu);
        $settingsManager->save($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * @param string $code
     * @param string $name
     * @param string $type
     * @param array $members
     *
     * @return ZoneInterface
     */
    protected function createZone($code, $name, $type, array $members)
    {
        /* @var $zone ZoneInterface */
        $zone = $this->getZoneFactory()->createWithMembers($members);
        $zone->setCode($code);
        $zone->setName($name);
        $zone->setType($type);

        $this->setReference('Sylius.Zone.'.$code, $zone);

        return $zone;
    }
}
