<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface AssetsInstallerInterface
{
    /**
     * Constant used as parameter and returned in installAssets() methods.
     *
     * @see AssetsInstallerInterface::installAssets()
     * @see AssetsInstallerInterface::installBundleAssets()
     * @see AssetsInstallerInterface::installDirAssets()
     */
    const HARD_COPY = 0;

    /**
     * Constant used as parameter and returned in installAssets() methods.
     *
     * @see AssetsInstallerInterface::installAssets()
     * @see AssetsInstallerInterface::installBundleAssets()
     * @see AssetsInstallerInterface::installDirAssets()
     */
    const SYMLINK = 1;

    /**
     * Constant used as parameter and returned in installAssets() methods.
     *
     * @see AssetsInstallerInterface::installAssets()
     * @see AssetsInstallerInterface::installBundleAssets()
     * @see AssetsInstallerInterface::installDirAssets()
     */
    const RELATIVE_SYMLINK = 2;

    /**
     * @param string $targetDir
     * @param int $symlinkMask
     *
     * @return int Effective symlink mask (lowest value received from installBundleAssets() method)
     */
    public function installAssets($targetDir, $symlinkMask);

    /**
     * @param BundleInterface $bundle
     * @param string $targetDir
     * @param int $symlinkMask
     *
     * @return int Effective symlink mask (lowest value received from installDirAssets() method)
     */
    public function installBundleAssets(BundleInterface $bundle, $targetDir, $symlinkMask);
}
