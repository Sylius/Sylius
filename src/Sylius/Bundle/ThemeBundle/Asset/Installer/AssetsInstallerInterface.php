<?php
namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Sylius\Bundle\ThemeBundle\Command\Model\SymlinkMask;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;


/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface AssetsInstallerInterface
{
    /**
     * TODO
     */
    const HARD_COPY = 0;

    /**
     * TODO
     */
    const SYMLINK = 1;

    /**
     * TODO
     */
    const RELATIVE_SYMLINK = 2;

    /**
     * @param string $targetDir
     * @param integer $symlinkMask
     *
     * @return integer Effective symlink mask (lowest value received from installBundleAssets() method)
     */
    public function installAssets($targetDir, $symlinkMask);

    /**
     * @param BundleInterface $bundle
     * @param string $targetDir
     * @param integer $symlinkMask
     *
     * @return integer Effective symlink mask (lowest value received from installDirAssets() method)
     */
    public function installBundleAssets(BundleInterface $bundle, $targetDir, $symlinkMask);

    /**
     * @param string $originDir
     * @param string $targetDir
     * @param integer $symlinkMask
     *
     * @return integer Effective symlink mask (relative symlink -> symlink -> hard copy)
     */
    public function installDirAssets($originDir, $targetDir, $symlinkMask);
}