<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader\Filesystem;

use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Loader\LoaderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FilesystemConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $configurationFilename;

    /**
     * @param FileLocatorInterface $fileLocator
     * @param LoaderInterface $loader
     * @param string $configurationFilename
     */
    public function __construct(FileLocatorInterface $fileLocator, LoaderInterface $loader, $configurationFilename)
    {
        $this->fileLocator = $fileLocator;
        $this->loader = $loader;
        $this->configurationFilename = $configurationFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurations()
    {
        return $this->processFileResources(function($file) {
            return $this->loader->load($file);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->processFileResources(function ($file) {
            return new FileResource($file);
        });
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    private function processFileResources(callable $callback)
    {
        try {
            $configurationFiles = $this->fileLocator->locateFilesNamed($this->configurationFilename);

            return array_map($callback, $configurationFiles);
        } catch (\InvalidArgumentException $exception) {
            return [];
        }
    }
}
