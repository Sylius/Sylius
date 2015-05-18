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
        foreach ($this->themes as $theme) {
            if ($logicalName === $theme->getLogicalName()) {
                return $theme;
            }
        }

        return null;
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
            $themes[] = $this->getTheme($logicalName);
        }

        return $themes;
    }

    /**
     * {@inheritdoc}
     */
    public function addTheme(ThemeInterface $theme, $priority = 0)
    {
        foreach ($this->themes as $existingTheme) {
            if ($existingTheme->equals($theme)) {
                return;
            }
        }

        $this->themes[] = $theme;
        $this->themesPriorities[$theme->getLogicalName()] = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTheme(ThemeInterface $theme)
    {
        foreach ($this->themes as $key => $existingTheme) {
            if ($existingTheme->equals($theme)) {
                unset($this->themes[$key]);
                unset($this->themesPriorities[$theme->getLogicalName()]);
                break;
            }
        }
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
        foreach ($this->themes as $key => $existingTheme) {
            if ($existingTheme->equals($theme)) {
                return true;
            }
        }

        return false;
    }
}