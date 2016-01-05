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

use SplFileInfo;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Loader\ThemeAwareLoader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

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
        $dirs = $this->findTranslationsDirs($container);

        if (empty($dirs)) {
            return;
        }

        $translator = $container->findDefinition('translator.default');

        // Register translation resources
        foreach ($dirs as $dir) {
            $container->addResource(new DirectoryResource($dir));
        }

        $files = [];
        $finder = Finder::create()
            ->files()
            ->filter(function (SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($dirs);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $locale = explode('.', $file->getBasename(), 3)[1];
            if (!isset($files[$locale])) {
                $files[$locale] = [];
            }

            $files[$locale][] = (string)$file;
        }

        $options = array_merge_recursive(
            $translator->getArgument(3),
            ['resource_files' => $files]
        );

        $translator->replaceArgument(3, $options);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function findTranslationsDirs(ContainerBuilder $container)
    {
        /** @var ThemeInterface[] $themes */
        $themes = $container->get('sylius.theme.repository')->findAll();

        $dirs = [];
        foreach ($themes as $theme) {
            foreach ($container->getParameter('kernel.bundles') as $bundle => $class) {
                if (is_dir($dir = $theme->getPath() . '/' . $bundle . '/translations')) {
                    $dirs[] = $dir;
                }
            }

            if (is_dir($dir = $theme->getPath() . '/translations')) {
                $dirs[] = $dir;
            }
        }

        return $dirs;
    }
}
