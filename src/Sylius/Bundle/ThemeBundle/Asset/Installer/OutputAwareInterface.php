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

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface OutputAwareInterface
{
    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}
