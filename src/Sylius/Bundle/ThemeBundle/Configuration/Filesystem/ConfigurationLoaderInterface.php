<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ConfigurationLoaderInterface
{
    /**
     * Loads configuration for given identifier (can be theme name or path to configuration file)
     *
     * @param string $identifier
     *
     * @return array
     */
    public function load($identifier);
}
