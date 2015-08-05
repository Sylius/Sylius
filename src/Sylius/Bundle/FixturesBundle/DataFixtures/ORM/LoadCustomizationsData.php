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

/**
 * Default assortment product properties to play with Sylius sandbox.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadCustomizationsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $customization = $this->createCustomization('Engraving');
        $customization->setType('text');
        $manager->persist($customization);

        $customization = $this->createCustomization('Message');
        $customization->setType('text');
        $manager->persist($customization);

        $customization = $this->createCustomization('Firstname');
        $customization->setType('text');
        $manager->persist($customization);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Create customization.
     *
     * @param string $name
     * @param string $presentation
     */
    private function createCustomization($name)
    {
        $repository = $this->getCustomizationRepository();

        $customization = $repository->createNew();
        $customization->setName($name);

        $this->setReference('Sylius.Customization.'.$name, $customization);

        return $customization;
    }
}
