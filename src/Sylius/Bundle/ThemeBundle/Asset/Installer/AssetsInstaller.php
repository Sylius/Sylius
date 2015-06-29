<?php

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolver;
use Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolverInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
     * @var ThemeDependenciesResolverInterface
     */
    protected $themeDependenciesResolver;

    /**
     * @var PathResolverInterface
     */
    protected $pathResolver;

    /**
     * @param Filesystem $filesystem
     * @param KernelInterface $kernel
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeDependenciesResolverInterface $themeDependenciesResolver
     * @param PathResolverInterface $pathResolver
     */
    public function __construct(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeRepositoryInterface $themeRepository,
        ThemeDependenciesResolverInterface $themeDependenciesResolver,
        PathResolverInterface $pathResolver
    ) {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->themeRepository = $themeRepository;
        $this->themeDependenciesResolver = $themeDependenciesResolver;
        $this->pathResolver = $pathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function installAssets($targetDir, $symlinkMask)
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
        foreach ($this->findAssetsPaths($bundle) as $originDir) {
            $effectiveSymlinkMask = min(
                $effectiveSymlinkMask,
                $this->installVanillaBundleAssets($originDir, $targetDir, $symlinkMask)
            );
        }

        foreach ($this->themeRepository->findAll() as $theme) {
            $themes = array_merge(
                [$theme],
                $this->themeDependenciesResolver->getDependencies($theme)
            );

            foreach ($this->findAssetsPaths($bundle, $themes) as $originDir) {
                $effectiveSymlinkMask = min(
                    $effectiveSymlinkMask,
                    $this->installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask)
                );
            }
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @param ThemeInterface $theme
     * @param string $originDir
     * @param string $targetDir
     * @param integer $symlinkMask
     *
     * @return integer
     */
    protected function installThemedBundleAssets(ThemeInterface $theme, $originDir, $targetDir, $symlinkMask)
    {
        $effectiveSymlinkMask = $symlinkMask;

        $finder = new Finder();
        $finder->sortByName()->ignoreDotFiles(false)->in($originDir);

        /** @var SplFileInfo[] $finder */
        foreach ($finder as $originFile) {
            $targetFile = $targetDir . '/' . $originFile->getRelativePathname();
            $targetFile = $this->pathResolver->resolve($targetFile, $theme);

            if (file_exists($targetFile)) {
                continue;
            }

            $this->filesystem->mkdir(dirname($targetFile));

            $effectiveSymlinkMask = min(
                $effectiveSymlinkMask,
                $this->installAsset($originFile->getPathname(), $targetFile, $symlinkMask)
            );
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     * @param integer $symlinkMask
     *
     * @return integer
     */
    protected function installVanillaBundleAssets($originDir, $targetDir, $symlinkMask)
    {
        return $this->installAsset($originDir, $targetDir, $symlinkMask);
    }

    /**
     * @param string $origin
     * @param string $target
     * @param integer $symlinkMask
     *
     * @return integer
     */
    protected function installAsset($origin, $target, $symlinkMask)
    {
        if (AssetsInstallerInterface::RELATIVE_SYMLINK === $symlinkMask) {
            try {
                $targetDirname = realpath(is_dir($target) ? $target : dirname($target));
                $relativeOrigin = rtrim($this->filesystem->makePathRelative($origin, $targetDirname), '/');

                $this->doInstallAsset($relativeOrigin, $target, true);

                return AssetsInstallerInterface::RELATIVE_SYMLINK;
            } catch (IOException $exception) {
                // Do nothing, trying to create non-relative symlinks later.
            }
        }

        if (AssetsInstallerInterface::HARD_COPY !== $symlinkMask) {
            try {
                $this->doInstallAsset($origin, $target, true);

                return AssetsInstallerInterface::SYMLINK;
            } catch (IOException $exception) {
                // Do nothing, hard copy later.
            }
        }

        $this->doInstallAsset($origin, $target, false);

        return AssetsInstallerInterface::HARD_COPY;
    }

    /**
     * @param string $origin
     * @param string $target
     * @param boolean $symlink
     *
     * @throws IOException When failed to make symbolic link, if requested.
     */
    protected function doInstallAsset($origin, $target, $symlink)
    {
        if ($symlink) {
            $this->filesystem->symlink($origin, $target);

            if (!file_exists($target)) {
                throw new IOException('Symbolic link is broken');
            }
        } else {
            if (is_dir($origin)) {
                $this->filesystem->mkdir($target, 0777);
                $this->filesystem->mirror($origin, $target, Finder::create()->ignoreDotFiles(false)->in($origin));
            } else {
                $this->filesystem->copy($origin, $target);
            }
        }
    }

    /**
     * @param BundleInterface $bundle
     * @param ThemeInterface[] $themes
     *
     * @return array
     */
    protected function findAssetsPaths(BundleInterface $bundle, array $themes = [])
    {
        $sources = [];

        foreach ($themes as $theme) {
            $sourceDir = $theme->getPath() . '/' . $bundle->getName() . '/public';
            if (is_dir($sourceDir)) {
                $sources[] = $sourceDir;
            }
        }

        $sourceDir = $bundle->getPath() . '/Resources/public';
        if (is_dir($sourceDir)) {
            $sources[] = $sourceDir;
        }

        return $sources;
    }
}