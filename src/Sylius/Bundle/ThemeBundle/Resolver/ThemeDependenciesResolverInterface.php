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
     * @throws \InvalidArgumentException If dependency could not be resolved.
     */
    public function resolveDependencies(ThemeInterface $theme);
}