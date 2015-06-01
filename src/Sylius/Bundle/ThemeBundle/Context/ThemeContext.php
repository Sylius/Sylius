<?php

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContext implements ThemeContextInterface
{
    /**
     * @var ThemeInterface[]
     */
    private $themes = [];

    /**
     * Keys are logical names of themes, values are priorities.
     *
     * @var array
     */
    private $themesPriorities = [];

    /**
     * {@inheritdoc}
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme($logicalName)
    {
        return isset($this->themes[$logicalName]) ? $this->themes[$logicalName] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemePriority($logicalName)
    {
        return isset($this->themesPriorities[$logicalName]) ? $this->themesPriorities[$logicalName] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemesPriorities()
    {
        return $this->themesPriorities;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemesSortedByPriorityInAscendingOrder()
    {
        return array_reverse($this->getThemesSortedByPriorityInDescendingOrder());
    }

    /**
     * {@inheritdoc}
     */
    public function getThemesSortedByPriorityInDescendingOrder()
    {
        $themes = [];

        $themesPriority = $this->themesPriorities;
        arsort($themesPriority);

        foreach ($themesPriority as $logicalName => $priority) {
            $themes[$logicalName] = $this->getTheme($logicalName);
        }

        return $themes;
    }

    /**
     * {@inheritdoc}
     */
    public function addTheme(ThemeInterface $theme, $priority = 0)
    {
        $this->themes[$theme->getLogicalName()] = $theme;
        $this->themesPriorities[$theme->getLogicalName()] = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTheme(ThemeInterface $theme)
    {
        unset($this->themes[$theme->getLogicalName()]);
        unset($this->themesPriorities[$theme->getLogicalName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllThemes()
    {
        $this->themes = [];
        $this->themesPriorities = [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasTheme(ThemeInterface $theme)
    {
        return isset($this->themes[$theme->getLogicalName()]);
    }
}