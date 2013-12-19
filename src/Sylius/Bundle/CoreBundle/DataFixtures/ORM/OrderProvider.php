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
 * Additional order Faker provider
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class OrderProvider extends AbstractProvider
{
    public function orderNumber($i)
    {
        return str_pad((int) $i, 9, 0, STR_PAD_LEFT);
    }
}
