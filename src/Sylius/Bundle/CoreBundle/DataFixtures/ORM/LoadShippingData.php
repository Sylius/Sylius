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
 * Default shipping fixtures.
 * Creates sample shipping categories and methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadShippingData extends AbstractDataFixture
{

    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/../DATA/shipping.yml',

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}