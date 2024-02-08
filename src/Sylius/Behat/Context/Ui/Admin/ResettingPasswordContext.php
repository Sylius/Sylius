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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\Account\ResetElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Account\RequestPasswordResetPage;
use Sylius\Behat\Page\Admin\Account\ResetPasswordPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

final class ResettingPasswordContext implements Context
{
    public function __construct(
        private RequestPasswordResetPage $requestPasswordResetPage,
        private ResetPasswordPageInterface $resetPasswordPage,
        private ResetElementInterface $resetElement,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->requestPasswordResetPage->open();
    }

    /**
     * @When I specify email as :email
     * @When I do not specify an email
     */
    public function iSpecifyEmailAs(string $email = ''): void
    {
        $this->requestPasswordResetPage->specifyEmail($email);
    }

    /**
     * @When /^I(?:| try to) reset it$/
     */
    public function iResetIt(): void
    {
        $this->resetElement->reset();
    }

    /**
     * @When /^(I)(?:| try to) follow the instructions to reset my password$/
     */
    public function iFollowTheInstructionsToResetMyPassword(AdminUserInterface $admin): void
    {
        $this->resetPasswordPage->tryToOpen(['token' => $admin->getPasswordResetToken()]);
    }

    /**
     * @When I specify my new password as :password
     * @When I do not specify my new password
     */
    public function iSpecifyMyNewPassword(string $password = ''): void
    {
        $this->resetPasswordPage->specifyNewPassword($password);
    }

    /**
     * @When I confirm my new password as :password
     * @When I do not confirm my new password
     */
    public function iConfirmMyNewPassword(string $password = ''): void
    {
        $this->resetPasswordPage->specifyPasswordConfirmation($password);
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionHasBeenSent(): void
    {
        $this->notificationChecker->checkNotification(
            'If the email you have specified exists in our system, we have sent there an instruction on how to reset your password.',
            NotificationType::success(),
        );
    }

    /**
     * @Then I should be notified that the email is required
     */
    public function iShouldBeNotifiedThatTheEmailIsRequired(): void
    {
        Assert::same(
            $this->requestPasswordResetPage->getEmailValidationMessage(),
            'Please enter an email.',
        );
    }

    /**
     * @Then I should be notified that the email is not valid
     */
    public function iShouldBeNotifiedThatTheEmailIsNotValid(): void
    {
        Assert::same(
            $this->requestPasswordResetPage->getEmailValidationMessage(),
            'This email is not valid.',
        );
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyChanged(): void
    {
        $this->notificationChecker->checkNotification(
            'Your password has been changed successfully!',
            NotificationType::success(),
        );
    }

    /**
     * @Then I should not be able to change my password again with the same token
     */
    public function iShouldNotBeAbleToChangeMyPasswordAgainWithTheSameToken(): void
    {
        $this->resetPasswordPage->tryToOpen(['token' => 'itotallyforgotmypassword']);

        Assert::false($this->resetPasswordPage->isOpen(), 'User should not be on the forgotten password page');
    }

    /**
     * @Then I should be notified that the password reset token has expired
     */
    public function iShouldBeNotifiedThatThePasswordResetTokenHasExpired(): void
    {
        $this->notificationChecker->checkNotification(
            'The password reset token has expired',
            NotificationType::failure(),
        );
    }

    /**
     * @Then I should be notified that the new password is required
     */
    public function iShouldBeNotifiedThatTheNewPasswordIsRequired(): void
    {
        Assert::contains(
            $this->resetPasswordPage->getValidationMessageForNewPassword(),
            'Please enter the password.',
        );
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch(): void
    {
        Assert::contains(
            $this->resetPasswordPage->getValidationMessageForNewPassword(),
            'The entered passwords do not match.',
        );
    }

    /**
     * @Then I should be notified that the password should be at least :length characters long
     */
    public function iShouldBeNotifiedThatThePasswordShouldBeAtLeastCharactersLong(int $length): void
    {
        Assert::true($this->resetPasswordPage->checkValidationMessageFor(
            'new_password',
            sprintf('Password must be at least %s characters long.', $length),
        ));
    }
}
