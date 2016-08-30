<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class EmailContext implements Context
{
    /**
     * @var EmailCheckerInterface
     */
    private $emailChecker;

    /**
     * @param EmailCheckerInterface $emailChecker
     */
    public function __construct(EmailCheckerInterface $emailChecker)
    {
        $this->emailChecker = $emailChecker;
    }

    /**
     * @Then it should be sent to :recipient
     * @Then the email with reset token should be sent to :recipient
     */
    public function anEmailShouldBeSentTo($recipient)
    {
        $this->assertEmailHasRecipient($recipient);
    }

    /**
     * @Then :count email(s) should be sent to :recipient
     */
    public function numberOfEmailsShouldBeSentTo($count, $recipient)
    {
        $this->assertEmailHasRecipient($recipient);

        Assert::eq(
            $this->emailChecker->getMessagesCount(),
            $count,
            sprintf(
                '%d messages were sent, while there should be %d.',
                $this->emailChecker->getMessagesCount(),
                $count
            )
        );
    }

    /**
     * @Then a welcoming email should have been sent to :recipient
     */
    public function aWelcomingEmailShouldHaveBeenSentTo($recipient)
    {
        $this->assertEmailHasRecipient($recipient);
        $this->assertEmailContainsMessage('Welcome to our store');

    }

    /**
     * @param string $recipient
     */
    private function assertEmailHasRecipient($recipient)
    {
        Assert::true(
            $this->emailChecker->hasRecipient($recipient),
            'An email should have been sent to %s.'
        );
    }

    /**
     * @param string $message
     */
    private function assertEmailContainsMessage($message)
    {
        Assert::true(
            $this->emailChecker->hasMessage($message),
            sprintf('Message "%s" was not found in any email.', $message)
        );
    }
}
