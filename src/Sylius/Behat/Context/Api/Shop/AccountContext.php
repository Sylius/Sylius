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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    /** @var ApiClientInterface */
    private $shopUserClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var Request */
    private $request;

    public function __construct(ApiClientInterface $shopUserClient, ResponseCheckerInterface $responseChecker)
    {
        $this->shopUserClient = $shopUserClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo(string $oldPassword, string $newPassword): void
    {
        $this->request->updateContent([
            'oldPassword' => $oldPassword,
            'password' => $newPassword,
            'confirmPassword' => $newPassword
        ]);
    }

    /**
     * @Given I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs(string $password): void
    {
        $this->request->updateContent(['oldPassword' => $password]);
    }

    /**
     * @Given I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs(string $password): void
    {
        $this->request->updateContent(['password' => $password]);
    }

    /**
     * @Given I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs(string $password): void
    {
        $this->request->updateContent(['confirmPassword' => $password]);
    }

    /**
     * @When I want to change my password
     */
    public function iWantToChangeMyPassword(): void
    {
        $this->request = Request::customUpdate('shop', null , null, 'change-password');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->shopUserClient->executeCustomRequest($this->request);
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyChanged(): void
    {
        Assert::same($this->shopUserClient->getLastResponse()->getStatusCode(), 204);
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne(): void
    {
        Assert::same($this->shopUserClient->getLastResponse()->getStatusCode(), 400);

        Assert::contains($this->responseChecker->getError($this->shopUserClient->getLastResponse()),
            'Provided password is different than the current one.');
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch(): void
    {
        Assert::same($this->shopUserClient->getLastResponse()->getStatusCode(), 400);

        Assert::contains($this->responseChecker->getError($this->shopUserClient->getLastResponse()),
            'Your password and confirmation password does not match.');
    }

    /**
     * @Then /^I should be notified that the ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe(string $elementName, string $validationMessage): void
    {
        Assert::contains($this->responseChecker->getError($this->shopUserClient->getLastResponse()),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage)
        );
    }
}
