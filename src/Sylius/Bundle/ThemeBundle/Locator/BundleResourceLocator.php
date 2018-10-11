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
    /** @var Filesystem */
    private $filesystem;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(Filesystem $filesystem, KernelInterface $kernel)
    {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource(string $resourcePath, ThemeInterface $theme): string
    {
        $this->assertResourcePathIsValid($resourcePath);

        if (false !== strpos($resourcePath, 'Bundle/Resources/views/')) {
            // When using bundle notation, we get a path like @AcmeBundle/Resources/views/template.html.twig
            return $this->locateResourceBasedOnBundleNotation($resourcePath, $theme);
        }

        // When using namespaced Twig paths, we get a path like @Acme/template.html.twig
        return $this->locateResourceBasedOnTwigNamespace($resourcePath, $theme);
    }

    private function assertResourcePathIsValid(string $resourcePath): void
    {
        if (0 !== strpos($resourcePath, '@')) {
            throw new \InvalidArgumentException(sprintf('Bundle resource path (given "%s") should start with an "@".', $resourcePath));
        }

        if (false !== strpos($resourcePath, '..')) {
            throw new \InvalidArgumentException(sprintf('File name "%s" contains invalid characters (..).', $resourcePath));
        }
    }

    private function locateResourceBasedOnBundleNotation(string $resourcePath, ThemeInterface $theme): string
    {
        $bundleName = substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
        $resourceName = substr($resourcePath, strpos($resourcePath, 'Resources/') + strlen('Resources/'));

        // Symfony 4.0+ always returns a single bundle
        $bundles = $this->kernel->getBundle($bundleName, false);

        // So we need to hack it to support both Symfony 3.4 and Symfony 4.0+
        if (!is_array($bundles)) {
            $bundles = [$bundles];
        }

        foreach ($bundles as $bundle) {
            $path = sprintf('%s/%s/%s', $theme->getPath(), $bundle->getName(), $resourceName);

            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new ResourceNotFoundException($resourcePath, $theme);
    }

    private function locateResourceBasedOnTwigNamespace(string $resourcePath, ThemeInterface $theme): string
    {
        $twigNamespace = substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
        $bundleName = $twigNamespace . 'Bundle';
        $resourceName = substr($resourcePath, strpos($resourcePath, '/') + 1);

        $path = sprintf('%s/%s/views/%s', $theme->getPath(), $bundleName, $resourceName);

        if ($this->filesystem->exists($path)) {
            return $path;
        }

        throw new ResourceNotFoundException($resourcePath, $theme);
    }
}
