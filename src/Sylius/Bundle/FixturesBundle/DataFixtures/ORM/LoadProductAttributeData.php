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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadProductAttributeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $attribute = $this->createAttribute('T-Shirt brand', array($this->defaultLocale => 'Brand', 'es_ES' => 'Marca'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt collection', array($this->defaultLocale => 'Collection', 'es_ES' => 'Coleccion'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('T-Shirt material', array($this->defaultLocale => 'Made of', 'es_ES' => 'Material'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker resolution', array($this->defaultLocale => 'Print resolution', 'es_ES' => 'Resolucion'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Sticker paper', array($this->defaultLocale => 'Paper', 'es_ES' => 'Papel'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Mug material', array($this->defaultLocale => 'Material', 'es_ES' => 'Material'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book author', array($this->defaultLocale => 'Author', 'es_ES' => 'Autor'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book ISBN', array($this->defaultLocale => 'ISBN', 'es_ES' => 'ISBN'));
        $manager->persist($attribute);

        $attribute = $this->createAttribute('Book pages', array($this->defaultLocale => 'Number of pages', 'es_ES' => 'Numero de paginas'));
        $manager->persist($attribute);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * Create attribute.
     *
     * @param string $name
     * @param array  $presentationTranslations
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
            $attribute->setFallbackLocale($locale);
            $attribute->setPresentation($presentation);
        }

        $this->setReference('Sylius.Attribute.'.$name, $attribute);

        return $attribute;
    }
}
