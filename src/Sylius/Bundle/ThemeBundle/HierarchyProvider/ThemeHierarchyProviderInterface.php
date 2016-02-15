<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\HierarchyProvider;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeHierarchyProviderInterface
{
    /**
     * @param ThemeInterface|null $theme
     *
     * @return ThemeInterface[]
     *
     * @throws \InvalidArgumentException If dependencies could not be resolved.
     */
    public function getThemeHierarchy(ThemeInterface $theme = null);
}
