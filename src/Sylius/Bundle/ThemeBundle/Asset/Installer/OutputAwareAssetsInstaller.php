<?php

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class OutputAwareAssetsInstaller extends AssetsInstaller implements OutputAwareAssetsInstallerInterface
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
            if (self::HARD_COPY !== $symlinkMask) {
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
        $sources = $this->findAssetsPathsForBundle($bundle);

        if (0 === count($sources)) {
            $effectiveSymlinkMask = $symlinkMask;
        } else {
            if ($this->hasOutput()) {
                $this->output->writeln(sprintf('Installing assets for <comment>%s</comment>', $bundle->getNamespace(), $targetDir));
            }

            $effectiveSymlinkMask = parent::installBundleAssets($bundle, $targetDir, $symlinkMask);

            if ($this->hasOutput()) {
                if (self::RELATIVE_SYMLINK === $symlinkMask) {
                    if (self::SYMLINK === $effectiveSymlinkMask) {
                        // Wanted to install assets using relative symbolic links, but used absolute symbolic links.
                        $this->output->writeln('It looks like your system doesn\'t support relative symbolic links, so the assets were installed by using absolute symbolic links.');
                        return $effectiveSymlinkMask;
                    }
                }

                if (self::HARD_COPY !== $symlinkMask) {
                    if (self::HARD_COPY !== $effectiveSymlinkMask) {
                        // Wanted to install assets using symbolic links (relative or absolute), and it was successful.
                        $this->output->writeln('The assets were installed using symbolic links.');
                        return $effectiveSymlinkMask;
                    } else {
                        // Wanted to install assets using symbolic links, but they was copied.
                        $this->output->writeln('It looks like your system doesn\'t support symbolic links, so the assets were installed by copying them.');
                        return $effectiveSymlinkMask;
                    }
                }

                if (self::HARD_COPY === $symlinkMask) {
                    if (self::HARD_COPY === $effectiveSymlinkMask) {
                        // Wanted to install assets by copying, and it was successfull.
                        $this->output->writeln('The assets were copied.');
                        return $effectiveSymlinkMask;
                    }
                }
            }
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
}