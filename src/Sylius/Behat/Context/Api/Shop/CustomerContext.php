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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Setup\ShopSecurityContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    /** @var ApiClientInterface */
    private $customerClient;

    /** @var ApiClientInterface */
    private $orderShopClient;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var RegistrationContext */
    private $registrationContext;

    /** @var LoginContext */
    private $loginContext;

    /** @var ShopSecurityContext */
    private $shopApiSecurityContext;

    public function __construct(
        ApiClientInterface $customerClient,
        ApiClientInterface $orderShopClient,
        SharedStorageInterface $sharedStorage,
        ResponseCheckerInterface $responseChecker,
        RegistrationContext $registrationContext,
        LoginContext $loginContext,
        ShopSecurityContext $shopApiSecurityContext
    ) {
        $this->customerClient = $customerClient;
        $this->orderShopClient = $orderShopClient;
        $this->sharedStorage = $sharedStorage;
        $this->responseChecker = $responseChecker;
        $this->registrationContext = $registrationContext;
        $this->loginContext = $loginContext;
        $this->shopApiSecurityContext = $shopApiSecurityContext;
    }

    /**
     * @When I want to modify my profile
     */
    public function iWantToModifyMyProfile(): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');
        $customer = $shopUser->getCustomer();

        $this->customerClient->buildUpdateRequest((string) $customer->getId());
    }

    /**
     * @When I want to change my password
     */
    public function iWantToChangeMyPassword(): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');
        /** @var CustomerInterface $customer */
        $customer = $shopUser->getCustomer();

        $this->customerClient->buildCustomUpdateRequest((string) $customer->getId(), 'password');
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName(string $firstName = ''): void
    {
        $this->customerClient->addRequestData('firstName', $firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName(string $lastName = ''): void
    {
        $this->customerClient->addRequestData('lastName', $lastName);
    }

    /**
     * @When I specify the customer email as :email
     * @When I remove the customer email
     */
    public function iSpecifyCustomerTheEmail(string $email = ''): void
    {
        $this->customerClient->addRequestData('email', $email);
    }

    /**
     * @When I (try to) save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->customerClient->update();
    }

    /**
     * @When I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs(string $password): void
    {
        $this->customerClient->addRequestData('currentPassword', $password);
    }

    /**
     * @When I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs(string $password): void
    {
        $this->customerClient->addRequestData('newPassword', $password);
    }

    /**
     * @When I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs(string $password): void
    {
        $this->customerClient->addRequestData('confirmNewPassword', $password);
    }

    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo(string $oldPassword, string $newPassword): void
    {
        $this->customerClient->setRequestData([
            'currentPassword' => $oldPassword,
            'newPassword' => $newPassword,
            'confirmNewPassword' => $newPassword,
        ]);
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->customerClient->getLastResponse()));
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe(string $email): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');

        $this->shopApiSecurityContext->iAmLoggedInAs($email);

        $response = $this->customerClient->show((string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'email', $email));
    }

    /**
     * @Then my name should be :name
     * @Then my name should still be :name
     */
    public function myNameShouldBe(string $name): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');

        $response = $this->customerClient->show((string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'fullName', $name));
    }

    /**
     * @Then I should be notified that the first name is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->customerClient->getLastResponse(),
            'First name must be at least 2 characters long.'
        ));
    }

    /**
     * @Then I should be notified that the last name is required
     */
    public function iShouldBeNotifiedThatLastNameIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->customerClient->getLastResponse(),
            'Last name must be at least 2 characters long.'
        ));
    }

    /**
     * @Then I should be notified that the email is required
     */
    public function iShouldBeNotifiedThatEmailIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->customerClient->getLastResponse(),
            'Please enter your email.'
        ));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->customerClient->getLastResponse(),
            'This email is already used.'
        ));
    }

    /**
     * @Then I should be notified that the email is invalid
     */
    public function iShouldBeNotifiedThatElementIsInvalid(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->customerClient->getLastResponse(),
            'This email is invalid.'
        ));
    }

    /**
     * @When I browse my orders
     */
    public function iBrowseMyOrders(): void
    {
        $this->orderShopClient->index();
    }

    /**
     * @When I register with previously used :email email and :password password
     */
    public function iRegisterWithPreviouslyUsedEmailAndPassword(string $email, string $password): void
    {
        $this->registrationContext->iWantToRegisterNewAccount();
        $this->registrationContext->iSpecifyTheEmailAs($email);
        $this->registrationContext->iSpecifyThePasswordAs($password);
        $this->registrationContext->iRegisterThisAccount();

        $this->loginContext->iLogInAsWithPassword($email, $password);
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->orderShopClient->index()), 1);
    }

    /**
     * @Then this order should have :orderNumber number
     */
    public function thisOrderShouldHaveNumber(string $orderNumber): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->orderShopClient->getLastResponse(),
                'number',
                $orderNumber
            )
        );
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyChanged(): void
    {
        $response = $this->customerClient->getLastResponse();

        Assert::same(
            $response->getStatusCode(),
            204,
            $response->getContent()
        );
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne(): void
    {
        Assert::same($this->customerClient->getLastResponse()->getStatusCode(), 400);

        Assert::contains(
            $this->responseChecker->getError($this->customerClient->getLastResponse()),
            'Provided password is different than the current one.'
        );
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch(): void
    {
        Assert::same($this->customerClient->getLastResponse()->getStatusCode(), 400);

        Assert::contains(
            $this->responseChecker->getError($this->customerClient->getLastResponse()),
            'newPassword: The entered passwords don\'t match'
        );
    }

    /**
     * @Then /^I should be notified that the ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe(string $elementName, string $validationMessage): void
    {
        Assert::contains($this->responseChecker->getError($this->customerClient->getLastResponse()),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage)
        );
    }

    private function isViolationWithMessageInResponse(Response $response, string $message): bool
    {
        $violations = $this->responseChecker->getResponseContent($response)['violations'];
        foreach ($violations as $violation) {
            if ($violation['message'] === $message) {
                return true;
            }
        }

        return false;
    }
}
