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
 * Default assortment product properties to play with Sylius sandbox.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadPropertiesData extends AbstractDataFixture
{

    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/../DATA/properties.yml',

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }

}