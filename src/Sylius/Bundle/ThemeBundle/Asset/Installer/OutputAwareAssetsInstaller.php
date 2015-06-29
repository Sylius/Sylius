<?php

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class OutputAwareAssetsInstaller extends AssetsInstaller implements OutputAwareInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

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
        if ($this->hasOutput()) {
            if (AssetsInstallerInterface::HARD_COPY !== $symlinkMask) {
                $this->output->writeln('Trying to install assets as <comment>symbolic links</comment>.');
            } else {
                $this->output->writeln('Installing assets as <comment>hard copies</comment>.');
            }
        }

        return parent::installAssets($targetDir, $symlinkMask);
    }

    /**
     * {@inheritdoc}
     */
    public function installBundleAssets(BundleInterface $bundle, $targetDir, $symlinkMask)
    {
        $sources = $this->findAssetsPaths($bundle);

        if (empty($sources)) {
            return $symlinkMask;
        }

        if ($this->hasOutput()) {
            $this->output->writeln(sprintf('Installing assets for <comment>%s</comment>', $bundle->getNamespace(), $targetDir));
        }

        $effectiveSymlinkMask = parent::installBundleAssets($bundle, $targetDir, $symlinkMask);

        if ($this->hasOutput()) {
            $this->output->writeln($this->provideResultComment($symlinkMask, $effectiveSymlinkMask));
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @return boolean
     */
    private function hasOutput()
    {
        return null !== $this->output;
    }

    /**
     * @param integer $symlinkMask
     * @param integer $effectiveSymlinkMask
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
}