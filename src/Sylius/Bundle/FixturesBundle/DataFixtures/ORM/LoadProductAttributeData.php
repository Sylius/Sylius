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
use Sylius\Component\Product\Model\AttributeInterface;

/**
 * Default product attributes to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
     *
     * @return AttributeInterface
     */
    protected function createAttribute($name, $presentation)
    {
        /* @var $attribute AttributeInterface */
        $attribute = $this->getProductAttributeRepository()->createNew();

        $attribute->setName($name);
        $attribute->setPresentation($presentation);

        $this->setReference('Sylius.Attribute.'.$name, $attribute);

        return $attribute;
    }
}
