<?php

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AssetsInstaller implements AssetsInstallerInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @var KernelInterface
     */
    protected $kernel;
    
    /**
     * @var ThemeRepositoryInterface
     */
    protected $themeRepository;

    /**
     * @var PathResolverInterface
     */
    protected $pathResolver;

    /**
     * @param Filesystem $filesystem
     * @param KernelInterface $kernel
     * @param ThemeRepositoryInterface $themeRepository
     * @param PathResolverInterface $pathResolver
     */
    public function __construct(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeRepositoryInterface $themeRepository,
        PathResolverInterface $pathResolver
    ) {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->themeRepository = $themeRepository;
        $this->pathResolver = $pathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function installAssets($targetDir = 'web', $symlinkMask)
    {
        // Create the bundles directory otherwise symlink will fail.
        $targetDir = rtrim($targetDir, '/') . '/bundles/';
        $this->filesystem->mkdir($targetDir);

        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->kernel->getBundles() as $bundle) {
            $effectiveSymlinkMask = min($effectiveSymlinkMask, $this->installBundleAssets($bundle, $targetDir, $symlinkMask));
        }

        return $effectiveSymlinkMask;
    }

    /**
     * {@inheritdoc}
     */
    public function installBundleAssets(BundleInterface $bundle, $targetDir, $symlinkMask)
    {
        $targetDir .= preg_replace('/bundle$/', '', strtolower($bundle->getName()));

        $this->filesystem->remove($targetDir);

        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->findAssetsPathsForBundle($bundle) as $originDir) {
            $effectiveSymlinkMask = min($effectiveSymlinkMask, $this->installDirAssets($originDir, $targetDir, $symlinkMask));
        }

        return $effectiveSymlinkMask;
    }

    /**
     * {@inheritdoc}
     */
    public function installDirAssets($originDir, $targetDir, $symlinkMask)
    {
        try {
            if (self::RELATIVE_SYMLINK === $symlinkMask) {
                try {
                    $this->relativeSymlinkAssets($originDir, $targetDir);

                    return self::RELATIVE_SYMLINK;
                } catch (IOException $exception) {
                    // Do nothing, trying to create non-relative symlinks later.
                }
            }

            if (self::HARD_COPY !== $symlinkMask) {
                $this->symlinkAssets($originDir, $targetDir);

                return self::SYMLINK;
            }
        } catch (IOException $exception) {
            // Do nothing, hard copy later.
        }

        $this->hardCopyAssets($originDir, $targetDir);

        return self::HARD_COPY;
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     */
    protected function relativeSymlinkAssets($originDir, $targetDir)
    {
        $this->doInstallDirAssets($originDir, $targetDir, true, true);
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     */
    protected function symlinkAssets($originDir, $targetDir)
    {
        $this->doInstallDirAssets($originDir, $targetDir, true, false);
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     */
    protected function hardCopyAssets($originDir, $targetDir)
    {
        $this->doInstallDirAssets($originDir, $targetDir, false, false);
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     * @param boolean $symlink
     * @param boolean $relativeSymlink
     */
    protected function doInstallDirAssets($originDir, $targetDir, $symlink, $relativeSymlink)
    {
        $finder = new Finder();
        $finder->sortByName()->ignoreDotFiles(false)->in($originDir);

        $theme = $this->themeRepository->findByPath($originDir);

        $this->filesystem->mkdir($targetDir);

        /** @var SplFileInfo[] $finder */
        foreach ($finder as $file) {
            if ($file->isDir()) {
                $this->filesystem->mkdir($targetDir . '/' . $file->getRelativePathname());
            } else {
                $targetFile = $targetDir . '/' . $file->getRelativePathname();
                if (null !== $theme) {
                    $targetFile = $this->pathResolver->resolve($targetFile, $theme);
                }

                $originFile = $file->getPathname();
                if ($relativeSymlink) {
                    $originFile = rtrim($this->filesystem->makePathRelative($originFile, realpath(dirname($targetFile))), '/');
                }

                if ($symlink) {
                    $this->filesystem->symlink($originFile, $targetFile);

                    if (!file_exists($targetFile)) {
                        throw new IOException('Symbolic link is broken');
                    }
                } else {
                    $this->filesystem->copy($originFile, $targetFile);
                }
            }
        }
    }

    /**
     * @param BundleInterface $bundle
     *
     * @return array
     */
    protected function findAssetsPathsForBundle(BundleInterface $bundle)
    {
        $sources[] = $bundle->getPath() . '/Resources/public';

        foreach ($this->themeRepository->findAll() as $theme) {
            $sources[] = $theme->getPath() . '/' . $bundle->getName() . '/public';
        }

        return array_filter($sources, function ($dir) {
            return is_dir($dir);
        });
    }
}