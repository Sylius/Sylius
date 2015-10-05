<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Checker;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface CommandDirectoryCheckerInterface
{
    /**
     * @param string          $directory
     * @param OutputInterface $output
     */
    public function ensureDirectoryExists($directory, OutputInterface $output);

    /**
     * @param string          $directory
     * @param OutputInterface $output
     */
    public function ensureDirectoryIsWritable($directory, OutputInterface $output);

    /**
     * @param string $name
     */
    public function setCommandName($name);
}
