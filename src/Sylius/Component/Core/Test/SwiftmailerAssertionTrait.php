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

namespace Sylius\Component\Core\Test;

use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

trait SwiftmailerAssertionTrait
{
    private static ?string $spoolDirectory = null;

    public static function assertSpooledMessagesHaveRecipient(string $recipient): void
    {
        self::assertRecipientIsValid($recipient);

        try {
            $messages = self::getMessages(self::getSpoolDirectory());
            foreach ($messages as $message) {
                if (self::isMessageTo($message, $recipient)) {
                    return;
                }
            }
        } catch (DirectoryNotFoundException) {
        }

        throw new ExpectationFailedException(sprintf('No message spooled with recipient "%s"', $recipient));
    }

    public static function assertSpooledMessageWithContentHasRecipient(string $message, string $recipient): void
    {
        self::assertRecipientIsValid($recipient);

        $messages = self::getMessages(self::getSpoolDirectory());
        foreach ($messages as $sentMessage) {
            if (self::isMessageTo($sentMessage, $recipient)) {
                $body = strip_tags($sentMessage->getBody());
                $body = str_replace("\n", ' ', $body);
                $body = preg_replace('/ {2,}/', ' ', $body);

                if (str_contains($body, $message)) {
                    return;
                }
            }
        }

        throw new ExpectationFailedException(sprintf(
            'No message spooled with recipient "%s" and content "%s"',
            $recipient,
            $message,
        ));
    }

    public static function assertSpooledMessagesCountWithRecipient(int $expectedCount, string $recipient): void
    {
        self::assertRecipientIsValid($recipient);

        $messagesCount = 0;

        $messages = self::getMessages(self::getSpoolDirectory());
        foreach ($messages as $message) {
            if (self::isMessageTo($message, $recipient)) {
                ++$messagesCount;
            }
        }

        self::assertEquals($expectedCount, $messagesCount);
    }

    public static function getSpoolDirectory(): string
    {
        Assert::notNull(
            self::$spoolDirectory,
            'Spool directory needs to be configured. Use the `setSpoolDirectory` method.',
        );

        return self::$spoolDirectory;
    }

    protected static function setSpoolDirectory(string $spoolDirectory): void
    {
        self::$spoolDirectory = $spoolDirectory;
    }

    private static function isMessageTo(object $message, string $recipient): bool
    {
        return array_key_exists($recipient, $message->getTo());
    }

    private static function assertRecipientIsValid(string $recipient): void
    {
        Assert::notEmpty($recipient, 'The recipient cannot be empty.');
        Assert::notEq(
            false,
            filter_var($recipient, \FILTER_VALIDATE_EMAIL),
            'Given recipient is not a valid email address.',
        );
    }

    private static function getMessages(string $directory): array
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
