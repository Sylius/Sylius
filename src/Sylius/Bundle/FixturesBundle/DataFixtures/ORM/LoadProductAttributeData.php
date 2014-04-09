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

/**
 * Default product attributes to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadProductAttributeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $attribute = $this->createAttribute('T-Shirt brand', 'Brand');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt collection', 'Collection');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt material', 'Made of');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker resolution', 'Print resolution');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker paper', 'Paper');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Mug material', 'Material');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book author', 'Author');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book ISBN', 'ISBN');
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book pages', 'Number of pages');
        $manager->persist($attribute);

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
     * Create attribute.
     *
     * @param string $name
     * @param string $presentation
     */
    private function createAttribute($name, $presentation)
    {
        $repository = $this->getProductAttributeRepository();

        $attribute = $repository->createNew();
        $attribute->setName($name);
        $attribute->setPresentation($presentation);

        $this->setReference('Sylius.Attribute.'.$name, $attribute);

        return $attribute;
    }
}
