<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

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
     * @var ConfigurationLoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $configurationFilename;

    /**
     * @param FileLocatorInterface $fileLocator
     * @param ConfigurationLoaderInterface $loader
     * @param string $configurationFilename
     */
    public function __construct(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader, $configurationFilename)
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
        try {
            return array_map(
                [$this->loader, 'load'],
                $this->fileLocator->locateFilesNamed($this->configurationFilename)
            );
        } catch (\InvalidArgumentException $exception) {
            return [];
        }
    }
}
