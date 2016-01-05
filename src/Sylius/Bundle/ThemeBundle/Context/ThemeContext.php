<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeContext implements ThemeContextInterface
{
    /**
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * @var ThemeInterface
     */
    private $theme;

    /**
     * @param ThemeHierarchyProviderInterface $themeHierarchyProvider
     */
    public function __construct(ThemeHierarchyProviderInterface $themeHierarchyProvider)
    {
        $this->themeHierarchyProvider = $themeHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setTheme(ThemeInterface $theme)
    {
        $this->theme = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeHierarchy()
    {
        return null !== $this->theme ? $this->themeHierarchyProvider->getThemeHierarchy($this->theme) : [];
    }
}
