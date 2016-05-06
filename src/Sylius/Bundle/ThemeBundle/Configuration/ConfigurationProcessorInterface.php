<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ConfigurationProcessorInterface
{
    /**
     * @param array $configs An array of configuration arrays
     *
     * @return array The processed configuration array
     */
    public function process(array $configs);
}
