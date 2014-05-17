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
use Sylius\Component\Product\Model\PrototypeInterface;

/**
 * Sample product prototypes.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadProductPrototypeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $prototype = $this->createPrototype('T-Shirt', array('T-Shirt size', 'T-Shirt color'), array('T-Shirt brand', 'T-Shirt collection', 'T-Shirt material'));
        $manager->persist($prototype);

        $prototype = $this->createPrototype('Sticker', array('Sticker size'), array('Sticker resolution', 'Sticker paper'));
        $manager->persist($prototype);

        $prototype = $this->createPrototype('Mug', array('Mug type'), array('Mug material'));
        $manager->persist($prototype);

        $prototype = $this->createPrototype('Book', array(), array('Book author', 'Book ISBN', 'Book pages'));
        $manager->persist($prototype);

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
     * Create prototype.
     *
     * @param string $name
     * @param array  $options
     * @param array  $properties
     *
     * @return PrototypeInterface
     */
    protected function createPrototype($name, array $options, array $properties)
    {
        /* @var $prototype PrototypeInterface */
        $prototype = $this->getProductPrototypeRepository()->createNew();
        $prototype->setName($name);

        foreach ($options as $option) {
            $prototype->addOption($this->getReference('Sylius.Option.'.$option));
        }
        foreach ($properties as $attribute) {
            $prototype->addAttribute($this->getReference('Sylius.Attribute.'.$attribute));
        }

        $this->setReference('Sylius.Prototype.'.$name, $prototype);

        return $prototype;
    }
}
