<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Command\Helper;

use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Output\OutputInterface;

trait EnsureDirectoryExistsAndIsWritable
{
    /**
     * @var CommandDirectoryChecker
     */
    private $commandDirectoryChecker;

    /**
     * @var string
     */
    private $commandName;

    /**
     * @param CommandDirectoryChecker $commandDirectoryChecker
     * @param string                  $commandName
     */
    public function __construct(CommandDirectoryChecker $commandDirectoryChecker, string $commandName)
    {
        $this->commandDirectoryChecker = $commandDirectoryChecker;
        $this->commandName = $commandName;
    }

    /**
     * @param string          $directory
     * @param OutputInterface $output
     */
    private function ensureDirectoryExistsAndIsWritable($directory, OutputInterface $output)
    {
        $checker = $this->commandDirectoryChecker;
        $checker->setCommandName($this->commandName);

        $checker->ensureDirectoryExists($directory, $output);
        $checker->ensureDirectoryIsWritable($directory, $output);
    }
}
