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

use Nelmio\Alice\Fixtures;

/**
 * Default exchange rate fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadExchangeRatesData extends AbstractDataFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/../DATA/exchange_rates.yml',

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

}