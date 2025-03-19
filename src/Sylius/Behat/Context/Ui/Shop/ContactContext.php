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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Contact\ContactPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

final readonly class ContactContext implements Context
{
    public function __construct(
        private ContactPageInterface $contactPage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I want to request contact
     */
    public function iWantToRequestContact(): void
    {
        $this->contactPage->open();
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail(string $email = ''): void
    {
        $this->contactPage->fillElement($email, 'email');
    }

    /**
     * @When I specify the message as :message
     * @When I do not specify the message
     */
    public function iSpecifyTheMessage(string $message = ''): void
    {
        $this->contactPage->fillElement($message, 'message');
    }

    /**
     * @When I send it
     * @When I try to send it
     */
    public function iSendIt(): void
    {
        $this->contactPage->send();
    }

    /**
     * @Then I should be notified that the contact request has been submitted successfully
     */
    public function iShouldBeNotifiedThatTheContactRequestHasBeenSubmittedSuccessfully(): void
    {
        $this->notificationChecker->checkNotification(
            'Your contact request has been submitted successfully.',
            NotificationType::success(),
        );
    }

    /**
     * @Then /^I should be notified that the (email|message) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::same($this->contactPage->getValidationMessage($element), sprintf('Please enter your %s.', $element));
    }

    /**
     * @Then I should be notified that the email is invalid
     */
    public function iShouldBeNotifiedThatEmailIsInvalid(): void
    {
        Assert::same($this->contactPage->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then I should be notified that a problem occurred while sending the contact request
     */
    public function iShouldBeNotifiedThatAProblemOccurredWhileSendingTheContactRequest(): void
    {
        $this->notificationChecker->checkNotification(
            'A problem occurred while sending the contact request. Please try again later.',
            NotificationType::error(),
        );
    }
}
