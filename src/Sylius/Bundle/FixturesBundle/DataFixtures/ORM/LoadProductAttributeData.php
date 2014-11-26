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
        $attribute = $this->createAttribute('T-Shirt brand', array('en' => 'Brand', 'es' => 'Marca'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt collection', array('en' => 'Collection', 'es' => 'Coleccion'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt material', array('en' => 'Made of', 'es' => 'Material'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker resolution', array('en' => 'Print resolution', 'es' => 'Resolucion'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker paper', array('en' => 'Paper', 'es' => 'Papel'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Mug material', array('en' => 'Material', 'es' => 'Material'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book author', array('en' => 'Author', 'es' => 'Autor'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book ISBN', array('en' => 'ISBN', 'es' => 'ISBN'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book pages', array('en' => 'Number of pages', 'es' => 'Numero de paginas'));
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
    protected function createAttribute($name, array $presentationTranslations)
    {
        /* @var $attribute AttributeInterface */
        $attribute = $this->getProductAttributeRepository()->createNew();
        $attribute->setName($name);

        foreach ($presentationTranslations as $locale => $presentation) {
            $attribute->setCurrentLocale($locale);
            $attribute->setPresentation($presentation);
        }

        $this->setReference('Sylius.Attribute.'.$name, $attribute);

        return $attribute;
    }
}
