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

final class DirectoryChecker
{
    /**
     * @var CommandDirectoryChecker
     */
    private $commandDirectoryChecker;

    public function __construct(CommandDirectoryChecker $commandDirectoryChecker)
    {
        $this->commandDirectoryChecker = $commandDirectoryChecker;
    }

    public function ensureDirectoryExistsAndIsWritable(string $directory, OutputInterface $output, string $commandName): void
    {
        $checker = $this->commandDirectoryChecker;
        $checker->setCommandName($commandName);

        $checker->ensureDirectoryExists($directory, $output);
        $checker->ensureDirectoryIsWritable($directory, $output);
    }
}
