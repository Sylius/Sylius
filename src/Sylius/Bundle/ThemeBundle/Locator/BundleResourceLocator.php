<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Locator;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class BundleResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param Filesystem $filesystem
     * @param KernelInterface $kernel
     */
    public function __construct(Filesystem $filesystem, KernelInterface $kernel)
    {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $resourcePath Eg. "@AcmeBundle/Resources/views/template.html.twig"
     */
    public function locateResource(string $resourcePath, ThemeInterface $theme): string
    {
        $this->assertResourcePathIsValid($resourcePath);

        $bundleName = $this->getBundleNameFromResourcePath($resourcePath);
        $resourceName = $this->getResourceNameFromResourcePath($resourcePath);

        $bundles = $this->kernel->getBundle($bundleName, false);
        foreach ($bundles as $bundle) {
            $path = sprintf('%s/%s/%s', $theme->getPath(), $bundle->getName(), $resourceName);

            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new ResourceNotFoundException($resourcePath, $theme);
    }

    /**
     * @param string $resourcePath
     */
    private function assertResourcePathIsValid(string $resourcePath): void
    {
        if (0 !== strpos($resourcePath, '@')) {
            throw new \InvalidArgumentException(sprintf('Bundle resource path (given "%s") should start with an "@".', $resourcePath));
        }

        if (false !== strpos($resourcePath, '..')) {
            throw new \InvalidArgumentException(sprintf('File name "%s" contains invalid characters (..).', $resourcePath));
        }

        if (false === strpos($resourcePath, 'Resources/')) {
            throw new \InvalidArgumentException(sprintf('Resource path "%s" should be in bundles\' "Resources/" directory.', $resourcePath));
        }
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    private function getBundleNameFromResourcePath(string $resourcePath): string
    {
        return substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    private function getResourceNameFromResourcePath(string $resourcePath): string
    {
        return substr($resourcePath, strpos($resourcePath, 'Resources/') + strlen('Resources/'));
    }
}
