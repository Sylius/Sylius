<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Checker;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CommandDirectoryChecker
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectoryExists($directory, OutputInterface $output)
    {
        if (!is_dir($directory)) {
            $this->createDirectory($directory, $output);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectoryIsWritable($directory, OutputInterface $output)
    {
        try {
            $this->changePermissionsRecursively($directory, $output);
        } catch (AccessDeniedException $exception) {
            $output->writeln('');
            $output->writeln($this->createBadPermissionsMessage());

            throw new \RuntimeException(
                sprintf('Set "%s" writable recursively and run command "<comment>%s</comment>"', $exception->getMessage(), $this->name)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCommandName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $directory
     * @param OutputInterface $output
     */
    private function createDirectory($directory, OutputInterface $output)
    {
        try {
            $this->filesystem->mkdir($directory, 0755);
        } catch (IOException $exception) {
            $output->writeln('');
            $output->writeln($this->createUnexistingDirectoryMessage());

            throw new \RuntimeException(
                sprintf('Create directory "%s" and run command "<comment>%s</comment>"', realpath($directory), $this->name)
            );
        }

        $output->writeln(sprintf('<comment>Created "%s" directory.</comment>', realpath($directory)));
    }

    /**
     * @param string $directory
     * @param OutputInterface $output
     */
    private function changePermissionsRecursively($directory, OutputInterface $output)
    {
        if (is_file($directory) && is_writable($directory)) {
            return;
        }

        if (!is_writable($directory)) {
            $this->changePermissions($directory, $output);

            return;
        }

        foreach (new RecursiveDirectoryIterator($directory, \FilesystemIterator::CURRENT_AS_FILEINFO) as $subdirectory) {
            if ('.' !== $subdirectory->getFilename()[0]) {
                $this->changePermissionsRecursively($subdirectory->getPathname(), $output);
            }
        }
    }

    /**
     * @param string $directory
     * @param OutputInterface $output
     *
     * @throws AccessDeniedException if directory/file permissions cannot be changed
     */
    private function changePermissions($directory, OutputInterface $output)
    {
        try {
            $this->filesystem->chmod($directory, 0755, 0000, true);

            $output->writeln(sprintf('<comment>Changed "%s" permissions to 0755.</comment>', realpath($directory)));
        } catch (IOException $exception) {
            throw new AccessDeniedException(realpath(dirname($directory)));
        }
    }

    /**
     * @return string
     */
    private function createUnexistingDirectoryMessage()
    {
        return '<error>Cannot run command due to unexisting directory (tried to create it automatically, failed).</error>'.PHP_EOL;
    }

    /**
     * @return string
     */
    private function createBadPermissionsMessage()
    {
        return '<error>Cannot run command due to bad directory permissions (tried to change permissions to 0755).</error>'.PHP_EOL;
    }
}
