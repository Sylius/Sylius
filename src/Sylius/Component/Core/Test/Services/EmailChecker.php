<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

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
            if (array_key_exists($recipient, $message->getTo())) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        $messages = $this->getMessages($this->spoolDirectory);
        foreach ($messages as $sentMessage) {
            if (false !== strpos($sentMessage->getBody(), $message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessagesCount()
    {
        return count($this->getMessages($this->spoolDirectory));
    }

    /**
     * {@inheritdoc}
     */
    public function getSpoolDirectory()
    {
        return $this->spoolDirectory;
    }

    /**
     * @param string $directory
     *
     * @return \Swift_Message[]
     */
    private function getMessages($directory)
    {
        $finder = new Finder();
        $finder->files()->name('*.message')->in($directory);
        Assert::notEq($finder->count(), 0, sprintf('No message files found in %s.', $directory));
        $messages = [];

        /** @var SplFileInfo $file */
        foreach($finder as $file) {
            $messages[] = unserialize($file->getContents());
        }

        return $messages;
    }
}
