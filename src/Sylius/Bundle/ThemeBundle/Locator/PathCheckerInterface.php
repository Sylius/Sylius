<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface PathCheckerInterface
{
    /**
     * @param array $paths
     * @param array $parameters
     * @param ThemeInterface[] $themes
     *
     * @return string|null
     */
    public function processPaths(array $paths, array $parameters, array $themes = []);

    /**
     * @param string $path
     * @param array $parameters
     * @param ThemeInterface[] $themes
     *
     * @return string|null
     */
    public function processPath($path, array $parameters, array $themes = []);
}
