<?php

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use SplFileInfo;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeTranslationCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
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

        $files = array();
        $finder = Finder::create()
            ->files()
            ->filter(function (SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($dirs)
        ;

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $locale = explode('.', $file->getBasename(), 3)[1];
            if (!isset($files[$locale])) {
                $files[$locale] = array();
            }

            $files[$locale][] = (string) $file;
        }

        $options = array_merge_recursive(
            $translator->getArgument(3),
            array('resource_files' => $files)
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
        /** @var ThemeRepositoryInterface $themeRepository */
        $themeRepository = $container->get('sylius.theme.repository');
        $themes = $themeRepository->findAll();

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