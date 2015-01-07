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
 */
class LoadProductArchetypeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $archetype = $this->createArchetype('t_shirt', 'T-Shirt', array('T-Shirt size', 'T-Shirt color'), array('T-Shirt brand', 'T-Shirt collection', 'T-Shirt material'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('sticker', 'Sticker', array('Sticker size'), array('Sticker resolution', 'Sticker paper'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('mug', 'Mug', array('Mug type'), array('Mug material'));
        $manager->persist($archetype);

        $archetype = $this->createArchetype('book', 'Book', array(), array('Book author', 'Book ISBN', 'Book pages'));
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
     * @param string $name
     * @param array  $options
     * @param array  $properties
     *
     * @return ArchetypeInterface
     */
    protected function createArchetype($code, $name, array $options, array $properties)
    {
        $archetype = $this->getProductArchetypeRepository()->createNew();
        $archetype
            ->setCode($code)
            ->setName($name)
        ;

        foreach ($options as $option) {
            $archetype->addOption($this->getReference('Sylius.Option.'.$option));
        }
        foreach ($properties as $attribute) {
            $archetype->addAttribute($this->getReference('Sylius.Attribute.'.$attribute));
        }

        $this->setReference('Sylius.Archetype.'.$name, $archetype);

        return $archetype;
    }
}
