<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractExtension extends Extension
{
    /**
     * Path where are stored your serive definition files.
     *
     * @var string
     */
    protected $configDirectory = '/../Resources/config';

    /**
     * Load the services service definitions
     *
     * @param ContainerBuilder $containerBuilder
     * @param string|array     $files
     *
     * @throws FileLoaderLoadException
     */
    protected function loadServiceDefinitions(ContainerBuilder $containerBuilder, $files)
    {
        $locator = new FileLocator($this->getDefinitionPath($containerBuilder));

        $resolver = new LoaderResolver(
            array(
                new XmlFileLoader($containerBuilder, $locator),
                new YamlFileLoader($containerBuilder, $locator),
            ) + $this->getExtraLoaders($containerBuilder)
        );

        $loader = new DelegatingLoader($resolver);

        if (!is_array($files)) {
            $files = array($files);
        }

        foreach ($files as $file) {
            $loader->load($file);
        }
    }

    /**
     * Load the driver service definitions
     *
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadDriverDefinition(ContainerBuilder $containerBuilder, $driver)
    {
        list($lib, $type) = explode('/', $driver);
        $rootPath = sprintf('%s/driver/%s', $this->getDefinitionPath(), $lib);

        if (!is_dir($rootPath)) {
            return;
        }

        $finder = new Finder();
        $files = $finder
            ->files()
            ->in($rootPath)
            ->name('/^'.$type.'\.*/')
        ;

        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $this->loadServiceDefinitions($containerBuilder, sprintf('driver/%s/%s', $lib, $file->getFilename()));
        }
    }

    /**
     * Get the configuration directory.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getDefinitionPath()
    {
        $reflector = new \ReflectionClass($this);
        $fileName = $reflector->getFileName();

        if (!is_dir($directory = dirname($fileName).$this->configDirectory)) {
            throw new \RuntimeException(sprintf('The configuration directory "%s" does not exists.', $directory));
        }

        return $directory;
    }

    /**
     * Register extra loaders
     *
     * @param ContainerBuilder $containerBuilder
     *
     * @return array
     */
    protected function getExtraLoaders(ContainerBuilder $containerBuilder)
    {
        return array();
    }
}
