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
use Sylius\Component\Product\Model\ArchetypeInterface;

/**
 * Sample product archetype.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadProductArchetypeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $archetype = $this->createArchetype('t_shirt', [$this->defaultLocale => 'T-Shirt', 'es' => 'Camiseta'], ['t_shirt_size', 't_shirt_color'], ['t_shirt_brand', 't_shirt_collection', 't_shirt_material']);
        $manager->persist($archetype);

        $archetype = $this->createArchetype('sticker', [$this->defaultLocale => 'Sticker', 'es' => 'Pegatina'], ['sticker_size'], ['sticker_resolution', 'sticker_paper']);
        $manager->persist($archetype);

        $archetype = $this->createArchetype('mug', [$this->defaultLocale => 'Mug', 'es' => 'Taza'], ['mug_type'], ['mug_material']);
        $manager->persist($archetype);

        $archetype = $this->createArchetype('book', [$this->defaultLocale => 'Book', 'es' => 'Libro'], [], ['book_author', 'book_isbn', 'book_pages']);
        $manager->persist($archetype);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 30;
    }

    /**
     * Create archetype.
     *
     * @param string $code
     * @param array  $nameTranslations
     * @param array  $options
     * @param array  $properties
     *
     * @return ArchetypeInterface
     */
    protected function createArchetype($code, array $nameTranslations, array $options, array $properties)
    {
        $archetype = $this->getProductArchetypeFactory()->createNew();
        $archetype->setCode($code);

        foreach ($nameTranslations as $locale => $name) {
            $archetype->setCurrentLocale($locale);
            $archetype->setName($name);
        }

        foreach ($options as $option) {
            $archetype->addOption($this->getReference('Sylius.Option.'.$option));
        }
        foreach ($properties as $attribute) {
            $archetype->addAttribute($this->getReference('Sylius.Attribute.'.$attribute));
        }

        $this->setReference('Sylius.Archetype.'.$code, $archetype);

        return $archetype;
    }
}
