<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Synchronizer;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeMergerInterface
{
    /**
     * Overrides every property of existing theme with corresponding loaded theme property.
     * Leaves id property of existing theme unchanged.
     *
     * @param ThemeInterface $existingTheme
     * @param ThemeInterface $loadedTheme
     *
     * @return ThemeInterface The existing theme instance
     */
    public function merge(ThemeInterface $existingTheme, ThemeInterface $loadedTheme);
}
