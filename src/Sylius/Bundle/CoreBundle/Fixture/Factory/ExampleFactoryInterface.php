<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ExampleFactoryInterface
{
    /**
     * @param array $options
     *
     * @return object
     */
    public function create(array $options = []);
}
