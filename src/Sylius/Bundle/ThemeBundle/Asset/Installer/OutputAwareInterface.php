<?php

namespace Sylius\Bundle\ThemeBundle\Asset\Installer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface OutputAwareInterface
{
    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}