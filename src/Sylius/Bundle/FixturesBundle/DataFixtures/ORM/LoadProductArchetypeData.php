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
        $archetype = $this->createArchetype('t_shirt', array($this->defaultLocale => 'T-Shirt', 'es' => 'Camiseta'), array('T-Shirt size', 'T-Shirt color'), array('T-Shirt brand', 'T-Shirt collection', 'T-Shirt material'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('sticker', array($this->defaultLocale => 'Sticker', 'es' => 'Pegatina'), array('Sticker size'), array('Sticker resolution', 'Sticker paper'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('mug', array($this->defaultLocale => 'Mug', 'es' => 'Taza'), array('Mug type'), array('Mug material'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('book', array($this->defaultLocale => 'Book', 'es' => 'Libro'), array(), array('Book author', 'Book ISBN', 'Book pages'));
        $manager->persist($archetype);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
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
        $archetype = $this->getProductArchetypeRepository()->createNew();
        $archetype->setCode($code);

        foreach ($nameTranslations as $locale => $name) {
            $archetype->setCurrentLocale($locale)
                ->setName($name);
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
