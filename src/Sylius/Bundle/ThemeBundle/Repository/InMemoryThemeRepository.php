<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class InMemoryThemeRepository implements ThemeRepositoryInterface
{
    /**
     * @var ThemeInterface[]
     */
    private $themes = [];

    /**
     * @var ThemeLoaderInterface
     */
    private $themeLoader;

    /**
     * @var bool
     */
    private $themesLoaded = false;

    /**
     * @param ThemeLoaderInterface $themeLoader
     */
    public function __construct(ThemeLoaderInterface $themeLoader)
    {
        $this->themeLoader = $themeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $this->loadThemesIfNeeded();

        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        $this->loadThemesIfNeeded();

        return isset($this->themes[$name]) ? $this->themes[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTitle($title)
    {
        $this->loadThemesIfNeeded();

        foreach ($this->themes as $theme) {
            if ($theme->getTitle() === $title) {
                return $theme;
            }
        }

        return null;
    }

    private function loadThemesIfNeeded()
    {
        if ($this->themesLoaded) {
            return;
        }

        $themes = $this->themeLoader->load();
        foreach ($themes as $theme) {
            $this->themes[$theme->getName()] = $theme;
        }

        $this->themesLoaded = true;
    }
}
