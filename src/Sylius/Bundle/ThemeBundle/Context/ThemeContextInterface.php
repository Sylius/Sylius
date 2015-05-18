<?php

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeContextInterface
{
    /**
     * Returns themes sorted in adding order.
     *
     * @return ThemeInterface[]
     */
    public function getThemes();

    /**
     * @param string $logicalName
     *
     * @return ThemeInterface|null
     */
    public function getTheme($logicalName);

    /**
     * @param string $logicalName
     *
     * @return integer|null Null if theme with given logical name was not found.
     */
    public function getThemePriority($logicalName);

    /**
     * @return integer[] Priorities array with themes logical name as keys.
     */
    public function getThemesPriorities();

    /**
     * Returns themes sorted by priority, from lowest to highest.
     *
     * @return ThemeInterface[]
     */
    public function getThemesSortedByPriorityInAscendingOrder();

    /**
     * Returns themes sorted by priority, from highest to lowest..
     *
     * @return ThemeInterface[]
     */
    public function getThemesSortedByPriorityInDescendingOrder();

    /**
     * @param ThemeInterface $theme
     * @param integer $priority
     */
    public function addTheme(ThemeInterface $theme, $priority = 0);

    /**
     * @param ThemeInterface $theme
     */
    public function removeTheme(ThemeInterface $theme);

    /**
     * Removes all themes.
     */
    public function removeAllThemes();

    /**
     * @param ThemeInterface $theme
     *
     * @return boolean
     */
    public function hasTheme(ThemeInterface $theme);
}