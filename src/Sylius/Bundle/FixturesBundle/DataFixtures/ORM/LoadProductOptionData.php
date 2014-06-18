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
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;

/**
 * Default product options to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadProductOptionData extends DataFixture
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
     *
     * @return OptionInterface
     */
    protected function createOption($name, $presentation, array $values)
    {
        /* @var $option OptionInterface */
        $option = $this->getProductOptionRepository()->createNew();
        $option->setName($name);
        $option->setPresentation($presentation);

        foreach ($values as $text) {
            /* @var $value OptionValueInterface */
            $value = $this->getProductOptionValueRepository()->createNew();
            $value->setValue($text);

            $option->addValue($value);
        }

        $this->setReference('Sylius.Option.'.$name, $option);

        return $option;
    }
}
