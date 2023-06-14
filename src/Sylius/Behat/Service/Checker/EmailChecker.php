<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service\Checker;

use Sylius\Behat\Service\Provider\EmailMessagesProviderInterface;
use Symfony\Component\Mime\Email;
use Webmozart\Assert\Assert;

final class EmailChecker implements EmailCheckerInterface
{
    public function __construct(private EmailMessagesProviderInterface $emailMessagesProvider)
    {
    }

    public function hasRecipient(string $recipient): bool
    {
        $messages = $this->emailMessagesProvider->provide();

        foreach ($messages as $email) {
            if ($this->isMessageTo($email, $recipient)) {
                return true;
            }
        }

        return false;
    }

    public function hasMessageTo(string $message, string $recipient): bool
    {
        $this->assertRecipientIsValid($recipient);

        $messages = $this->emailMessagesProvider->provide();

        foreach ($messages as $email) {
            if ($this->isMessageTo($email, $recipient)) {
                $emailTextContent = trim(preg_replace('/\n+\s+/', ' ', strip_tags($email->getHtmlBody())));

                if (str_contains($emailTextContent, $message)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function countMessagesTo(string $recipient): int
    {
        $this->assertRecipientIsValid($recipient);

        $messagesCount = 0;
        $messages = $this->emailMessagesProvider->provide();

        foreach ($messages as $email) {
            if ($this->isMessageTo($email, $recipient)) {
                ++$messagesCount;
            }
        }

        return $messagesCount;
    }

    private function isMessageTo(Email $message, string $recipient): bool
    {
        foreach ($message->getTo() as $toRecipient) {
            if ($recipient === $toRecipient->getAddress()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertRecipientIsValid(string $recipient): void
    {
        Assert::notEmpty($recipient, 'The recipient cannot be empty.');
        Assert::notEq(
            false,
            filter_var($recipient, \FILTER_VALIDATE_EMAIL),
            'Given recipient is not a valid email address.',
        );
    }
}
