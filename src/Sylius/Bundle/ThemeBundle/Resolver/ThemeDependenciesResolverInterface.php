<?php

namespace Sylius\Bundle\ThemeBundle\Resolver;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeDependenciesResolverInterface
{
    /**
     * @param ThemeInterface $theme
     *
     * @return ThemeInterface[]
     *
     * @throws \InvalidArgumentException If dependencies could not be resolved.
     */
    public function getDependencies(ThemeInterface $theme);
}