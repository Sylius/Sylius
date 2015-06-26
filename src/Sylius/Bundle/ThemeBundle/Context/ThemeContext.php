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
        $this->themeDependenciesResolver->resolveDependencies($theme);

        $this->themes = [$theme->getLogicalName() => $theme];

        $this->addThemeDependencies($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemes()
    {
        return $this->themes ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllThemes()
    {
        $this->themes = [];
    }

    /**
     * Adds theme dependencies to context.
     *
     * @param ThemeInterface $theme
     */
    protected function addThemeDependencies(ThemeInterface $theme)
    {
        foreach ($theme->getParents() as $parent) {
            $this->themes[$parent->getLogicalName()] = $parent;

            $this->addThemeDependencies($parent);
        }
    }
}