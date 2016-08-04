<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Resource;

use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\ThemeTranslationResource;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeTranslatorResourceProvider implements TranslatorResourceProviderInterface
{
    /**
     * @var TranslationFilesFinderInterface
     */
    private $translationFilesFinder;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * @param TranslationFilesFinderInterface $translationFilesFinder
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeHierarchyProviderInterface $themeHierarchyProvider
     */
    public function __construct(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider
    ) {
        $this->translationFilesFinder = $translationFilesFinder;
        $this->themeRepository = $themeRepository;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        /** @var ThemeInterface[] $themes */
        $themes = $this->themeRepository->findAll();

        $resources = [];
        foreach ($themes as $theme) {
            $resources = array_merge($resources, $this->extractResourcesFromTheme($theme));
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesLocales()
    {
        return array_values(array_unique(array_map(function (TranslationResourceInterface $translationResource) {
            return $translationResource->getLocale();
        }, $this->getResources())));
    }

    /**
     * @param ThemeInterface $mainTheme
     *
     * @return array
     */
    private function extractResourcesFromTheme(ThemeInterface $mainTheme)
    {
        /** @var ThemeInterface[] $themes */
        $themes = array_reverse($this->themeHierarchyProvider->getThemeHierarchy($mainTheme));

        $resources = [];
        foreach ($themes as $theme) {
            $paths = $this->translationFilesFinder->findTranslationFiles($theme->getPath());

            foreach ($paths as $path) {
                $resources[] = new ThemeTranslationResource($mainTheme, $path);
            }
        }

        return $resources;
    }
}
