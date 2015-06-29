<?php

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolverInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContext implements ThemeContextInterface
{
    /**
     * @var ThemeInterface[]
     */
    protected $themes;

    /**
     * @var boolean
     */
    protected $areDependenciesResolved = true;

    /**
     * @var ThemeDependenciesResolverInterface
     */
    protected $themeDependenciesResolver;

    /**
     * @param ThemeDependenciesResolverInterface $themeDependenciesResolver
     */
    public function __construct(ThemeDependenciesResolverInterface $themeDependenciesResolver)
    {
        $this->themeDependenciesResolver = $themeDependenciesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function setTheme(ThemeInterface $theme)
    {
        $this->themes = [$theme];
        $this->areDependenciesResolved = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        return !empty($this->themes) ? $this->themes[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemes()
    {
        if (empty($this->themes)) {
            return [];
        }

        $this->ensureDependenciesAreResolved();

        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->themes = [];
    }

    /**
     * Resolves main theme dependencies if they aren't resolved yet.
     */
    protected function ensureDependenciesAreResolved()
    {
        if (false === $this->areDependenciesResolved) {
            $this->themes = array_merge(
                $this->themes,
                $this->themeDependenciesResolver->getDependencies($this->themes[0])
            );

            $this->areDependenciesResolved = true;
        }
    }
}