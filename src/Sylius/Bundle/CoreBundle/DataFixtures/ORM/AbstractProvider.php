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

use Faker\Factory as FakerFactory;

/**
 * Abstract additional Faker provider
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
abstract class AbstractProvider
{
    protected $locale;
    protected $faker;

    public function __construct($locale, $faker)
    {
        $this->locale = $locale;
        $this->faker = $faker;
    }
}