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
 * Default assortment product options to play with sandbox.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadOptionsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // T-Shirt size option.
        $option = $this->createOption('T-Shirt size', 'Size', array('S', 'M', 'L', 'XL', 'XXL'));
        $manager->persist($option);

        // T-Shirt color option.
        $option = $this->createOption('T-Shirt color', 'Color', array('Red', 'Blue', 'Green'));
        $manager->persist($option);

        // Sticker size option.
        $option = $this->createOption('Sticker size', 'Size', array('3"','5"','7"'));
        $manager->persist($option);

        // Mug type option.
        $option = $this->createOption('Mug type', 'Type', array('Medium mug','Double mug','MONSTER mug'));
        $manager->persist($option);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * Create an option.
     *
     * @param string $name
     * @param string $presentation
     * @param array  $values
     */
    private function createOption($name, $presentation, array $values)
    {
        $option = $this
            ->getOptionRepository()
            ->createNew()
        ;

        $option->setName($name);
        $option->setPresentation($presentation);

        foreach ($values as $text) {
            $value = $this->getOptionValueRepository()->createNew();
            $value->setValue($text);

            $option->addValue($value);
        }

        $this->setReference('Sylius.Option.'.$name, $option);

        return $option;
    }
}
