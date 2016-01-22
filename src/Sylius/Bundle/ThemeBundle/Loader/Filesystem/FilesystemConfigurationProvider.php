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
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FilesystemConfigurationProvider implements ConfigurationProviderInterface, CompilerPassInterface
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
    public function provideAll()
    {
        try {
            $configurationFiles = $this->fileLocator->locateFilesNamed($this->configurationFilename);

            return iterator_to_array($this->loadConfigurationFiles($configurationFiles));
        } catch (\InvalidArgumentException $exception) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $configurationFiles = $this->fileLocator->locateFilesNamed($this->configurationFilename);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        foreach ($configurationFiles as $configurationFile) {
            $container->addResource(new FileResource($configurationFile));
        }
    }

    /**
     * @return \Generator
     */
    private function loadConfigurationFiles(array $configurationFiles)
    {
        foreach ($configurationFiles as $configurationFile) {
            yield $this->loader->load($configurationFile);
        }
    }
}
