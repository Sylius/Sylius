<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Accessor;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class EmailChecker implements EmailCheckerInterface
{
    /**
     * @var string
     */
    private $spoolDirectory;

    /**
     * @param string $spoolDirectory
     */
    public function __construct($spoolDirectory)
    {
        $this->spoolDirectory = $spoolDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRecipient($recipient)
    {
        $messages = $this->getMessages($this->spoolDirectory);
        foreach ($messages as $message) {
            if (false !== strpos($message, $recipient)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    private function getMessages($directory)
    {
        $finder = new Finder();
        $finder->files()->name('*.message')->in($directory);
        $spools = [];

        /** @var SplFileInfo $file */
        foreach($finder as $file) {
            $spools[] = $file->getContents();
            unlink($file->getRealPath());
        }

        return $spools;
    }
}
