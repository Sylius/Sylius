<?php

namespace Sylius\Bundle\ThemeBundle\Locator;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class BundleResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var PathCheckerInterface
     */
    private $pathChecker;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $paths = [
        '%theme_path%/%bundle_name%/%override_path%',
        '%app_path%/Resources/%bundle_name%/%override_path%',
        '%bundle_path%/Resources/%override_path%',
    ];

    /**
     * @param PathCheckerInterface $pathChecker
     * @param KernelInterface $kernel
     * @param string $appDir
     */
    public function __construct(PathCheckerInterface $pathChecker, KernelInterface $kernel, $appDir)
    {
        $this->pathChecker = $pathChecker;
        $this->kernel = $kernel;
        $this->parameters = [
            "%app_path%" => $appDir,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($resourceName, array $themes = [])
    {
        if (false !== strpos($resourceName, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $resourceName));
        }

        $bundleName = substr($resourceName, 1);
        $resourcePath = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $resourcePath) = explode('/', $bundleName, 2);
        }
        if (0 !== strpos($resourcePath, 'Resources')) {
            throw new \RuntimeException('Template files have to be in Resources.');
        }

        $bundles = $this->kernel->getBundle($bundleName, false);

        $parameters = array_merge(
            $this->parameters,
            ['%override_path%' => substr($resourcePath, strlen('Resources/'))]
        );

        foreach ($bundles as $bundle) {
            $parameters = array_merge($parameters, [
                '%bundle_name%' => $bundle->getName(),
                '%bundle_path%' => $bundle->getPath(),
            ]);

            $checkedPath = $this->pathChecker->processPaths($this->paths, $parameters, $themes);
            if (null !== $checkedPath) {
                return $checkedPath;
            }
        }

        return null;
    }
}