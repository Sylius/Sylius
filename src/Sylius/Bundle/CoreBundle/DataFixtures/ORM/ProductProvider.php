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

/**
 * Additional product Faker provider
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProductProvider extends AbstractProvider
{
    public function price()
    {
        return $this->faker->randomNumber(500, 9999);
    }

    public function sku()
    {
        return $this->faker->randomNumber(8);
    }

    public function onHand()
    {
        return $this->faker->randomNumber(1);
    }

    public function availableOn()
    {
        return $this->faker->dateTimeThisYear();
    }

    public function tShirtBrand()
    {
        return $this->faker->randomElement(array('Nike', 'Adidas', 'Puma', 'Potato'));
    }

    public function tShirtCollection()
    {
        return sprintf('Symfony2 %s %s', $this->faker->randomElement(array('Summer', 'Winter', 'Spring', 'Autumn')), rand(1995, 2012));
    }

    public function tShirtMaterial()
    {
        return $this->faker->randomElement(array('Polyester', 'Wool', 'Polyester 10% / Wool 90%', 'Potato 100%'));
    }

    public function stickerResolution()
    {
        return $this->faker->randomElement(array('Waka waka', 'FULL HD', '300DPI', '200DPI'));
    }

    public function stickerPaper()
    {
        return sprintf('Paper from tree %s', $this->faker->randomElement(array('Wung', 'Yang', 'Lemon-San', 'Me-Gusta')));
    }

    public function mugMaterial()
    {
        return $this->faker->randomElement(array('Invisible porcelain', 'Banana skin', 'Porcelain', 'Sand'));
    }

    public function isbn()
    {
        return $this->faker->randomNumber(13);
    }


}