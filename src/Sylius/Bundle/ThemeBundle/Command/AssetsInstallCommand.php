<?php

namespace Sylius\Bundle\ThemeBundle\Command;

use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\OutputAwareAssetsInstallerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AssetsInstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('x:assets:install')
            ->setDefinition(array(
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
            ))
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
            ->setDescription('Installs bundles web assets under a public web directory')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs bundle assets into a given
directory (e.g. the <comment>web</comment> directory).

  <info>php %command.full_name% web</info>

A "bundles" directory will be created inside the target directory and the
"Resources/public" directory of each bundle will be copied into it.

To create a symlink to each bundle instead of copying its assets, use the
<info>--symlink</info> option (will fall back to hard copies when symbolic links aren't possible:

  <info>php %command.full_name% web --symlink</info>

To make symlink relative, add the <info>--relative</info> option:

  <info>php %command.full_name% web --symlink --relative</info>

EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetsInstaller = $this->getContainer()->get('sylius.theme.assets_installer');
        if ($assetsInstaller instanceof OutputAwareAssetsInstallerInterface) {
            $assetsInstaller->setOutput($output);
        }

        $symlinkMask = AssetsInstallerInterface::HARD_COPY;

        if ($input->getOption('symlink')) {
            $symlinkMask = max($symlinkMask, AssetsInstallerInterface::SYMLINK);
        }

        if ($input->getOption('relative')) {
            $symlinkMask = max($symlinkMask, AssetsInstallerInterface::RELATIVE_SYMLINK);
        }

        $assetsInstaller->installAssets($input->getArgument('target'), $symlinkMask);
    }
}