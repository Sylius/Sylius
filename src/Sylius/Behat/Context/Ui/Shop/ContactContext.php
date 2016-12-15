<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\PageInterface;
use Sylius\Behat\Page\Shop\Contact\ContactPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ContactContext implements Context
{
    /**
     * @var ContactPageInterface
     */
    private $contactPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param ContactPageInterface $contactPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        ContactPageInterface $contactPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->contactPage = $contactPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to request contact
     */
    public function iWantToRequestContact()
    {
        $this->contactPage->open();
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->contactPage->specifyEmail($email);
    }

    /**
     * @When I specify the message as :message
     * @When I do not specify the message
     */
    public function iSpecifyTheMessage($message = null)
    {
        $this->contactPage->specifyMessage($message);
    }

    /**
     * @When I send it
     * @When I try to send it
     */
    public function iSendIt()
    {
        $this->contactPage->send();
    }

    /**
     * @Then I should be notified that the contact request has been submitted successfully
     */
    public function iShouldBeNotifiedThatTheContactRequestHasBeenSubmittedSuccessfully()
    {
        $this->notificationChecker->checkNotification(
            'Your request contact has been submitted successfully.',
            NotificationType::success()
        );
    }

    /**
     * @Then /^I should be notified that the (email|message) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage(
            $this->contactPage,
            $element,
            sprintf('Please enter your %s.', $element)
        );
    }

    /**
     * @Then /^I should be notified that the (email) is invalid$/
     */
    public function iShouldBeNotifiedThatElementIsInvalid($element)
    {
        $this->assertFieldValidationMessage(
            $this->contactPage,
            $element,
            sprintf('This %s is invalid.', $element)
        );
    }

    /**
     * @Then /^I should be notified that there was a problem with sending a contact request$/
     */
    public function iShouldBeNotifiedThatThereWasAProblemWithSendingAContactRequest()
    {
        $this->notificationChecker->checkNotification(
            'There was a problem with sending a contact request. Please try again later.',
            NotificationType::failure()
        );
    }

    /**
     * @param PageInterface $page
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage(PageInterface $page, $element, $expectedMessage)
    {
        Assert::true(
            $page->checkValidationMessageFor($element, $expectedMessage),
            sprintf('There should be a message: "%s".', $expectedMessage)
        );
    }
}
