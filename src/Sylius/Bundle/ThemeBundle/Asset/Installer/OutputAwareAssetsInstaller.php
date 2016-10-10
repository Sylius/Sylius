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

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OutputAwareAssetsInstaller implements AssetsInstallerInterface, OutputAwareInterface
{
    /**
     * @var AssetsInstallerInterface
     */
    private $assetsInstaller;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param AssetsInstallerInterface $assetsInstaller
     */
    public function __construct(AssetsInstallerInterface $assetsInstaller)
    {
        $this->assetsInstaller = $assetsInstaller;
        $this->output = new NullOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function installAssets($targetDir, $symlinkMask)
    {
        $this->output->writeln($this->provideExpectationComment($symlinkMask));

        return $this->assetsInstaller->installAssets($targetDir, $symlinkMask);
    }

    /**
     * {@inheritdoc}
     */
    public function installBundleAssets(BundleInterface $bundle, $targetDir, $symlinkMask)
    {
        $this->output->writeln(sprintf('Installing assets for <comment>%s</comment>', $bundle->getNamespace(), $targetDir));

        $effectiveSymlinkMask = $this->assetsInstaller->installBundleAssets($bundle, $targetDir, $symlinkMask);

        $this->output->writeln($this->provideResultComment($symlinkMask, $effectiveSymlinkMask));

        return $effectiveSymlinkMask;
    }

    /**
     * @param int $symlinkMask
     * @param int $effectiveSymlinkMask
     *
     * @return string
     */
    private function provideResultComment($symlinkMask, $effectiveSymlinkMask)
    {
        if ($effectiveSymlinkMask === $symlinkMask) {
            switch ($symlinkMask) {
                case AssetsInstallerInterface::HARD_COPY:
                    return 'The assets were copied.';
                case AssetsInstallerInterface::SYMLINK:
                    return 'The assets were installed using symbolic links.';
                case AssetsInstallerInterface::RELATIVE_SYMLINK:
                    return 'The assets were installed using relative symbolic links.';
            }
        }

        switch ($symlinkMask + $effectiveSymlinkMask) {
            case AssetsInstallerInterface::SYMLINK:
            case AssetsInstallerInterface::RELATIVE_SYMLINK:
                return 'It looks like your system doesn\'t support symbolic links, so the assets were copied.';
            case AssetsInstallerInterface::RELATIVE_SYMLINK + AssetsInstallerInterface::SYMLINK:
                return 'It looks like your system doesn\'t support relative symbolic links, so the assets were installed by using absolute symbolic links.';
        }

        return 'Something gone bad, can\'t provide the result of assets installing!';
    }

    /**
     * @param int $symlinkMask
     *
     * @return string
     */
    private function provideExpectationComment($symlinkMask)
    {
        if (AssetsInstallerInterface::HARD_COPY === $symlinkMask) {
            return 'Installing assets as <comment>hard copies</comment>.';
        }

        return 'Trying to install assets as <comment>symbolic links</comment>.';
    }
}
