<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAwareSourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->addThemeAwareTranslationSources($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addThemeAwareTranslationSources(ContainerBuilder $container)
    {
        $files = $this->getTranslationFiles($container);

        if (empty($files)) {
            return;
        }

        $groupedFiles = $this->groupFilesByLocale($files);

        $translator = $container->findDefinition('translator.default');

        $options = array_merge_recursive(
            $translator->getArgument(3),
            ['resource_files' => $groupedFiles]
        );

        $translator->replaceArgument(3, $options);

        $this->addContainerResources($container, $files);
    }

    private function getTranslationFiles(ContainerBuilder $container)
    {
        /** @var ConfigurationProviderInterface $configurationProvider */
        $configurationProvider = $container->get('sylius.theme.configuration.provider');

        /** @var ThemeFactoryInterface $themeFactory */
        $themeFactory = $container->get('sylius.factory.theme');

        $themes = [];
        foreach ($configurationProvider->getConfigurations() as $themeConfiguration) {
            $theme = $themeFactory->createNamed($themeConfiguration['name']);
            $theme->setPath($themeConfiguration['path']);

            $themes[] = $theme;
        }

        /** @var TranslationFilesFinderInterface $translationFilesFinder */
        $translationFilesFinder = $container->get('sylius.theme.translation.files_finder');
        $files = [];
        foreach ($themes as $theme) {
            $files = array_merge(
                $files,
                $translationFilesFinder->findTranslationFiles($theme)
            );
        }

        return $files;
    }

    /**
     * @param ContainerBuilder $container
     * @param array $files
     */
    private function addContainerResources(ContainerBuilder $container, $files)
    {
        foreach ($files as $file) {
            $container->addResource(new FileResource($file));
        }
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function groupFilesByLocale($files)
    {
        $groupedFiles = [];
        foreach ($files as $file) {
            $locale = explode('.', basename($file), 3)[1];
            if (!isset($groupedFiles[$locale])) {
                $groupedFiles[$locale] = [];
            }

            $groupedFiles[$locale][] = $file;
        }

        return $groupedFiles;
    }
}
