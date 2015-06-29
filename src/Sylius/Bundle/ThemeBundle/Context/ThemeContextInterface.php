<?php

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeContextInterface
{
    /**
     * @param ThemeInterface $theme
     */
    public function setTheme(ThemeInterface $theme);

    /**
     * @return ThemeInterface
     */
    public function getTheme();

    /**
     * Returns themes sorted by priority descending.
     *
     * @return ThemeInterface[]
     */
    public function getThemes();

    /**
     * Removes all themes found in context.
     */
    public function clear();
}