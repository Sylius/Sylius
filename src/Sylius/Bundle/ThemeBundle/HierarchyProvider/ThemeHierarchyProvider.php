<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\HierarchyProvider;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeHierarchyProvider implements ThemeHierarchyProviderInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeHierarchy(ThemeInterface $theme)
    {
        $parents = [];
        $parentsNames = $theme->getParentsNames();
        foreach ($parentsNames as $parentName) {
            $parents = array_merge(
                $parents,
                $this->getThemeHierarchy($this->getTheme($parentName))
            );
        }

        return array_merge([$theme], $parents);
    }

    /**
     * @param string $themeName
     *
     * @return ThemeInterface
     *
     * @throws \InvalidArgumentException If theme is not found
     */
    private function getTheme($themeName)
    {
        $theme = $this->themeRepository->findOneByName($themeName);

        if (null === $theme) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" not found!', $themeName));
        }

        return $theme;
    }
}
