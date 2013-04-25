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

/**
 * Default assortment product properties to play with Sylius sandbox.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadPropertiesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $property = $this->createProperty('T-Shirt brand', 'Brand');
        $manager->persist($property);

        $property = $this->createProperty('T-Shirt collection', 'Collection');
        $manager->persist($property);

        $property = $this->createProperty('T-Shirt material', 'Made of');
        $manager->persist($property);

        $property = $this->createProperty('Sticker resolution', 'Print resolution');
        $manager->persist($property);

        $property = $this->createProperty('Sticker paper', 'Paper');
        $manager->persist($property);

        $property = $this->createProperty('Mug material', 'Material');
        $manager->persist($property);

        $property = $this->createProperty('Book author', 'Author');
        $manager->persist($property);

        $property = $this->createProperty('Book ISBN', 'ISBN');
        $manager->persist($property);

        $property = $this->createProperty('Book pages', 'Number of pages');
        $manager->persist($property);

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
     * Create property.
     *
     * @param string $name
     * @param string $presentation
     */
    private function createProperty($name, $presentation)
    {
        $repository = $this->getPropertyRepository();

        $property = $repository->createNew();
        $property->setName($name);
        $property->setPresentation($presentation);

        $this->setReference('Sylius.Property.'.$name, $property);

        return $property;
    }
}
