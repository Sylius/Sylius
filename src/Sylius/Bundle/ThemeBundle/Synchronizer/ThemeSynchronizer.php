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

use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Provider\ThemeProviderInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Zend\Hydrator\HydrationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSynchronizer implements ThemeSynchronizerInterface
{
    /**
     * @var ConfigurationProviderInterface
     */
    private $configurationProvider;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var HydrationInterface
     */
    private $themeHydrator;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var CircularDependencyCheckerInterface
     */
    private $circularDependencyChecker;

    /**
     * @param ConfigurationProviderInterface $configurationProvider
     * @param ThemeProviderInterface $themeProvider
     * @param HydrationInterface $themeHydrator
     * @param ThemeRepositoryInterface $themeRepository
     * @param CircularDependencyCheckerInterface $circularDependencyChecker
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        HydrationInterface $themeHydrator,
        ThemeRepositoryInterface $themeRepository,
        CircularDependencyCheckerInterface $circularDependencyChecker
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->themeProvider = $themeProvider;
        $this->themeHydrator = $themeHydrator;
        $this->themeRepository = $themeRepository;
        $this->circularDependencyChecker = $circularDependencyChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronize()
    {
        $configurations = $this->configurationProvider->getConfigurations();

        $themes = $this->initializeThemes($configurations);
        $themes = $this->hydrateThemes($configurations, $themes);

        $this->checkForCircularDependencies($themes);

        foreach ($themes as $theme) {
            $this->themeRepository->add($theme);
        }
    }

    /**
     * @param array $configurations
     *
     * @return ThemeInterface[]
     */
    private function initializeThemes(array $configurations)
    {
        $themes = [];
        foreach ($configurations as $configuration) {
            /** @var ThemeInterface $theme */
            $themes[$configuration['name']] = $this->themeProvider->getNamed($configuration['name']);
        }

        return $themes;
    }

    /**
     * @param array $configurations
     * @param ThemeInterface[] $themes
     *
     * @return ThemeInterface[]
     */
    private function hydrateThemes(array $configurations, array $themes)
    {
        foreach ($configurations as $configuration) {
            if (!isset($configuration['parents'])) {
                $configuration['parents'] = [];
            }

            $configuration['parents'] = array_map(function ($parentName) use ($themes, $configuration) {
                if (!isset($themes[$parentName])) {
                    throw new SynchronizationFailedException(sprintf(
                        'Unexisting theme "%s" is required by "%s".',
                        $parentName,
                        $configuration['name']
                    ));
                }

                return $themes[$parentName];
            }, $configuration['parents']);

            $themes[$configuration['name']] = $this->themeHydrator->hydrate($configuration, $themes[$configuration['name']]);
        }

        return $themes;
    }

    /**
     * @param ThemeInterface[] $themes
     */
    private function checkForCircularDependencies(array $themes)
    {
        try {
            foreach ($themes as $theme) {
                $this->circularDependencyChecker->check($theme);
            }
        } catch (CircularDependencyFoundException $exception) {
            throw new SynchronizationFailedException('Circular dependency found.', 0, $exception);
        }
    }
}
