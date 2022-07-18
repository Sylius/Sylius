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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Account\RequestPasswordResetPage;
use Sylius\Behat\Page\Admin\Account\RequestPasswordResetPageInterface;
use Sylius\Behat\Page\Admin\Account\ResetPasswordPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

final class ResettingPasswordContext implements Context
{
    public function __construct(
        private RequestPasswordResetPage $requestPasswordResetPage,
        private ResetPasswordPageInterface $resetPasswordPage,
        private CurrentPageResolverInterface $currentPageResolver,
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
        /** @var RequestPasswordResetPageInterface|ResetPasswordPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->requestPasswordResetPage,
            $this->resetPasswordPage,
        ]);

        $currentPage->reset();
    }

    /**
     * @When /^(I) follow the instructions to reset my password$/
     */
    public function iFollowTheInstructionsToResetMyPassword(AdminUserInterface $admin): void
    {
        $this->resetPasswordPage->open(['token' => $admin->getPasswordResetToken()]);
    }

    /**
     * @When I specify my new password as :password
     */
    public function iSpecifyMyNewPassword(string $password): void
    {
        $this->resetPasswordPage->specifyNewPassword($password);
    }

    /**
     * @When I confirm my new password as :password
     */
    public function iConfirmMyNewPassword(string $password): void
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
            NotificationType::success()
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
        $this->notificationChecker->checkNotification('has been changed successfully!', NotificationType::success());
    }

    /**
     * @Then I should not be able to change my password again with the same token
     */
    public function iShouldNotBeAbleToChangeMyPasswordAgainWithTheSameToken(): void
    {
        $this->resetPasswordPage->tryToOpen(['token' => 'itotallyforgotmypassword']);

        Assert::false($this->resetPasswordPage->isOpen(), 'User should not be on the forgotten password page');
    }
}
