<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Test;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface TestThemeConfigurationManagerInterface
{
    /**
     * @return array
     */
    public function findAll();

    /**
     * @param array $configuration
     */
    public function add(array $configuration);

    /**
     * @param string $themeName
     */
    public function remove($themeName);

    /**
     * Clear currently used configurations storage.
     */
    public function clear();
}
