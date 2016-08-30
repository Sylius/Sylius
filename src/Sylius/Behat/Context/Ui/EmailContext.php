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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class EmailContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var EmailCheckerInterface
     */
    private $emailChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param EmailCheckerInterface $emailChecker
     */
    public function __construct(SharedStorageInterface $sharedStorage, EmailCheckerInterface $emailChecker)
    {
        $this->sharedStorage = $sharedStorage;
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
     * @Then an email with shipment's details of order :order should be sent to :recipient
     */
    public function anEmailWithShipmentDetailsOfOrderShouldBeSentTo(OrderInterface $order, $recipient)
    {
        $this->assertEmailHasRecipient($recipient);

        $this->assertEmailContainsMessage($order->getNumber());
        $this->assertEmailContainsMessage($order->getLastShipment()->getMethod()->getName());

        $tracking = $this->sharedStorage->get('tracking_code');
        $this->assertEmailContainsMessage($tracking);
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
