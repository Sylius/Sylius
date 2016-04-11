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
            't_shirt_size',
            'T-Shirt size',
            [$this->defaultLocale => 'T-Shirt Size', 'es_ES' => 'Talla'],
            [
                'OV1' => [$this->defaultLocale => 'S', 'es_ES' => 'S'],
                'OV2' => [$this->defaultLocale => 'M', 'es_ES' => 'M'],
                'OV3' => [$this->defaultLocale => 'L', 'es_ES' => 'L'],
                'OV4' => [$this->defaultLocale => 'XL', 'es_ES' => 'XL'],
                'OV5' => [$this->defaultLocale => 'XXL', 'es_ES' => 'XLL'],
            ]
        );
        $manager->persist($option);

        // T-Shirt color option.
        $option = $this->createOption(
            't_shirt_color',
            'T-Shirt color',
            [$this->defaultLocale => 'T-Shirt Color'],
            [
                'OV6' => [$this->defaultLocale => 'Red', 'es_ES' => 'Rojo'],
                'OV7' => [$this->defaultLocale => 'Blue', 'es_ES' => 'Azul'],
                'OV8' => [$this->defaultLocale => 'Green', 'es_ES' => 'Verde'],
            ]
        );
        $manager->persist($option);

        // Sticker size option.
        $option = $this->createOption(
            'sticker_size',
            'Sticker size',
            [$this->defaultLocale => 'Sticker Size', 'es_ES' => 'Talla'],
            [
                'OV9' => [$this->defaultLocale => '3"', 'es_ES' => '3"'],
                'OV10' => [$this->defaultLocale => '5"', 'es_ES' => '5"'],
                'OV11' => [$this->defaultLocale => '7"', 'es_ES' => '7"'],
            ]
        );
        $manager->persist($option);

        // Mug type option.
        $option = $this->createOption(
            'mug_type',
            'Mug type',
            [$this->defaultLocale => 'Mug Type', 'es_ES' => 'Tipo'],
            [
                'OV12' => [$this->defaultLocale => 'Medium mug', 'es_ES' => 'Taza mediana'],
                'OV13' => [$this->defaultLocale => 'Double mug', 'es_ES' => 'Taza doble'],
                'OV14' => [$this->defaultLocale => 'MONSTER mug', 'es_ES' => 'Taza del monstruo'],
            ]
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
     * @param array $nameTranslation
     * @param array $valuesData
     *
     * @return OptionInterface
     */
    protected function createOption($optionCode, $name, array $nameTranslation, array $valuesData)
    {
        /* @var $option OptionInterface */
        $option = $this->getProductOptionFactory()->createNew();
        $option->setCode($optionCode);

        foreach ($nameTranslation as $locale => $name) {
            $option->setFallbackLocale($locale);
            $option->setCurrentLocale($locale);
            $option->setName($name);
        }
        $option->setCurrentLocale($this->defaultLocale);

        foreach ($valuesData as $code => $values) {
            /* @var $values OptionValueInterface */
            $optionValue = $this->getProductOptionValueFactory()->createNew();
            $optionValue->setCode($code);

            foreach ($values as $locale => $value) {
                $optionValue->setFallbackLocale($locale);
                $optionValue->setCurrentLocale($locale);
                $optionValue->setValue($value);
            }
            $option->addValue($optionValue);
        }

        $this->setReference('Sylius.Option.'.$optionCode, $option);

        return $option;
    }
}
