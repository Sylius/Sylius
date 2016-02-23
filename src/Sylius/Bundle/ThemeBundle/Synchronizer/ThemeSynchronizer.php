<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Synchronizer;

use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSynchronizer implements ThemeSynchronizerInterface
{
    /**
     * @var ThemeLoaderInterface
     */
    private $themeLoader;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeMergerInterface
     */
    private $themeMerger;

    /**
     * @param ThemeLoaderInterface $themeLoader
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeMergerInterface $themeMerger
     */
    public function __construct(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeMergerInterface $themeMerger
    ) {
        $this->themeLoader = $themeLoader;
        $this->themeRepository = $themeRepository;
        $this->themeMerger = $themeMerger;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronize()
    {
        $persistedThemes = $this->themeRepository->findAll();
        $loadedThemes = $this->themeLoader->load();

        $removedThemes = $this->removeAbandonedThemes($persistedThemes, $loadedThemes);
        $existingThemes = array_udiff(
            $persistedThemes,
            $removedThemes,
            function (ThemeInterface $firstTheme, ThemeInterface $secondTheme) {
                return (int) ($firstTheme->getName() === $secondTheme->getName());
            }
        );

        $this->updateThemes($existingThemes, $loadedThemes);
    }

    /**
     * @param ThemeInterface[] $existingThemes
     * @param ThemeInterface[] $loadedThemes
     */
    private function updateThemes(array $existingThemes, array $loadedThemes)
    {
        $loadedThemes = $this->ensureCohesionOfReferencedThemes($existingThemes, $loadedThemes);

        foreach ($loadedThemes as $loadedTheme) {
            $this->updateTheme($loadedTheme);
        }
    }

    /**
     * @param ThemeInterface $theme
     */
    private function updateTheme(ThemeInterface $theme)
    {
        $existingTheme = $this->themeRepository->findOneByName($theme->getName());

        if (null !== $existingTheme) {
            $theme = $this->themeMerger->merge($existingTheme, $theme);
        }

        $this->themeRepository->add($theme);
    }

    /**
     * @param ThemeInterface[] $persistedThemes
     * @param ThemeInterface[] $loadedThemes
     *
     * @return ThemeInterface[] Removed themes
     */
    private function removeAbandonedThemes(array $persistedThemes, array $loadedThemes)
    {
        if (0 === count($persistedThemes)) {
            return [];
        }

        $loadedThemesNames = array_map(function (ThemeInterface $theme) {
            return $theme->getName();
        }, $loadedThemes);

        $removedThemes = [];
        foreach ($persistedThemes as $persistedTheme) {
            if (!in_array($persistedTheme->getName(), $loadedThemesNames, true)) {
                $removedThemes[] = $persistedTheme;
                $this->themeRepository->remove($persistedTheme);
            }
        }

        return $removedThemes;
    }

    /**
     * Removes references to loaded themes, that exists.
     * Adds references to existing themes instead (the loaded ones will be merged into them).
     *
     * @param ThemeInterface[] $existingThemes
     * @param ThemeInterface[] $loadedThemes
     *
     * @return ThemeInterface[]
     */
    private function ensureCohesionOfReferencedThemes(array $existingThemes, array $loadedThemes)
    {
        foreach ($loadedThemes as $loadedTheme) {
            foreach ($loadedTheme->getParents() as $parentTheme) {
                $correspondingTheme = current(array_filter(
                    array_merge($existingThemes, $loadedThemes),
                    function (ThemeInterface $theme) use ($parentTheme) {
                        return $theme->getName() === $parentTheme->getName();
                    }
                ));

                if (null === $correspondingTheme) {
                    throw new SynchronizationFailedException('Cannot find a corresponding theme!');
                }

                $loadedTheme->removeParent($parentTheme);
                $loadedTheme->addParent($correspondingTheme);
            }
        }

        return $loadedThemes;
    }
}
