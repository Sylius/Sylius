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

namespace Sylius\Component\Core\Test\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final class EmailChecker implements EmailCheckerInterface
{
    /**
     * @var string
     */
    private $spoolDirectory;

    /**
     * @param string $spoolDirectory
     */
    public function __construct(string $spoolDirectory)
    {
        $this->spoolDirectory = $spoolDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRecipient(string $recipient): bool
    {
        $this->assertRecipientIsValid($recipient);

        $messages = $this->getMessages($this->spoolDirectory);
        foreach ($messages as $message) {
            if ($this->isMessageTo($message, $recipient)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessageTo(string $message, string $recipient): bool
    {
        $this->assertRecipientIsValid($recipient);

        $messages = $this->getMessages($this->spoolDirectory);
        foreach ($messages as $sentMessage) {
            if ($this->isMessageTo($sentMessage, $recipient)) {
                if (false !== strpos($sentMessage->getBody(), $message)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function countMessagesTo(string $recipient): int
    {
        $this->assertRecipientIsValid($recipient);

        $messagesCount = 0;

        $messages = $this->getMessages($this->spoolDirectory);
        foreach ($messages as $message) {
            if ($this->isMessageTo($message, $recipient)) {
                ++$messagesCount;
            }
        }

        return $messagesCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpoolDirectory(): string
    {
        return $this->spoolDirectory;
    }

    /**
     * @param \Swift_Message $message
     * @param string $recipient
     *
     * @return bool
     */
    private function isMessageTo(\Swift_Message $message, string $recipient): bool
    {
        return array_key_exists($recipient, $message->getTo());
    }

    /**
     * @param string $recipient
     *
     * @throws \InvalidArgumentException
     */
    private function assertRecipientIsValid(string $recipient): void
    {
        Assert::notEmpty($recipient, 'The recipient cannot be empty.');
        Assert::string($recipient, sprintf('The recipient must be a string, %s given.', gettype($recipient)));
        Assert::notEq(
            false,
            filter_var($recipient, FILTER_VALIDATE_EMAIL),
            'Given recipient is not a valid email address.'
        );
    }

    /**
     * @param string $directory
     *
     * @return array|\Swift_Message[]
     */
    private function getMessages(string $directory): array
    {
        $finder = new Finder();
        $finder->files()->name('*.message')->in($directory);
        Assert::notEq($finder->count(), 0, sprintf('No message files found in %s.', $directory));
        $messages = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $messages[] = unserialize($file->getContents());
        }

        return $messages;
    }
}
