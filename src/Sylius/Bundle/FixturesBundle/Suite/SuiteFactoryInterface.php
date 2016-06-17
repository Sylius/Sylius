<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SuiteFactoryInterface
{
    /**
     * @param string $name
     * @param array $configuration
     *
     * @return SuiteInterface
     */
    public function createSuite($name, array $configuration);
}
