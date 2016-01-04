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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadProductOptionData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // T-Shirt size option.
        $option = $this->createOption(
            'O1',
            'T-Shirt size',
            array($this->defaultLocale => 'Size', 'es_ES' => 'Talla'),
            array(
                'OV1' => 'S',
                'OV2' => 'M',
                'OV3' => 'L',
                'OV4' => 'XL',
                'OV5' => 'XXL',
            )
        );
        $manager->persist($option);

        // T-Shirt color option.
        $option = $this->createOption(
            'O2',
            'T-Shirt color',
            array($this->defaultLocale => 'Color'),
            array(
                'OV6' => 'Red',
                'OV7' => 'Blue',
                'OV8' => 'Green',
            )
        );
        $manager->persist($option);

        // Sticker size option.
        $option = $this->createOption(
            'O3',
            'Sticker size',
            array($this->defaultLocale => 'Size', 'es_ES' => 'Talla'),
            array(
                'OV9' => '3"',
                'OV10' => '5"',
                'OV11' => '7"',
            )
        );
        $manager->persist($option);

        // Mug type option.
        $option = $this->createOption(
            'O4',
            'Mug type',
            array($this->defaultLocale => 'Type', 'es_ES' => 'Tipo'),
            array(
                'OV12' => 'Medium mug',
                'OV13' => 'Double mug',
                'OV14' => 'MONSTER mug',
            )
        );
        $manager->persist($option);

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
     * @param string $optionCode
     * @param string $name
     * @param array $presentationTranslation
     * @param array $valuesData
     *
     * @return OptionInterface
     */
    protected function createOption($optionCode, $name, array $presentationTranslation, array $valuesData)
    {
        /* @var $option OptionInterface */
        $option = $this->getProductOptionFactory()->createNew();
        $option->setName($name);
        $option->setCode($optionCode);

        foreach ($presentationTranslation as $locale => $presentation) {
            $option->setCurrentLocale($locale);
            $option->setPresentation($presentation);
        }
        $option->setCurrentLocale($this->defaultLocale);

        foreach ($valuesData as $code => $value) {
            /* @var $value OptionValueInterface */
            $optionValue = $this->getProductOptionValueFactory()->createNew();
            $optionValue->setValue($value);
            $optionValue->setCode($code);

            $option->addValue($optionValue);
        }

        $this->setReference('Sylius.Option.'.$name, $option);

        return $option;
    }
}
