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
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    /** @var ApiClientInterface */
    private $accountClient;

    /** @var ApiClientInterface */
    private $orderShopClient;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var ShopSecurityContext */
    private $shopApiSecurityContext;

    public function __construct(
        ApiClientInterface $accountClient,
        ApiClientInterface $orderShopClient,
        SharedStorageInterface $sharedStorage,
        ResponseCheckerInterface $responseChecker,
        ShopSecurityContext $shopApiSecurityContext
    ) {
        $this->accountClient = $accountClient;
        $this->orderShopClient = $orderShopClient;
        $this->sharedStorage = $sharedStorage;
        $this->responseChecker = $responseChecker;
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

        $this->accountClient->buildUpdateRequest((string) $customer->getId());
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName(string $firstName = ''): void
    {
        $this->accountClient->addRequestData('firstName', $firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName(string $lastName = ''): void
    {
        $this->accountClient->addRequestData('lastName', $lastName);
    }

    /**
     * @When I specify the customer email as :email
     * @When I remove the customer email
     */
    public function iSpecifyCustomerTheEmail(string $email = ''): void
    {
        $this->accountClient->addRequestData('email', $email);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->accountClient->update();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->accountClient->getLastResponse()));
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

        $response = $this->accountClient->show((string) $shopUser->getCustomer()->getId());

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

        $response = $this->accountClient->show((string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'fullName', $name));
    }

    /**
     * @Then I should be notified that the first name is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->accountClient->getLastResponse(),
            'First name must be at least 2 characters long.'
        );
    }

    /**
     * @Then I should be notified that the last name is required
     */
    public function iShouldBeNotifiedThatLastNameIsRequired(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->accountClient->getLastResponse(),
            'Last name must be at least 2 characters long.'
        );
    }

    /**
     * @Then I should be notified that the email is required
     */
    public function iShouldBeNotifiedThatEmailIsRequired(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->accountClient->getLastResponse(),
            'Please enter your email.'
        );
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->accountClient->getLastResponse(),
            'This email is already used.'
        );
    }

    /**
     * @Then I should be notified that the email is invalid
     */
    public function iShouldBeNotifiedThatElementIsInvalid(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->accountClient->getLastResponse(),
            'This email is invalid.'
        );
    }

    /**
     * @When I browse my orders
     */
    public function iBrowseMyOrders(): void
    {
        $this->orderShopClient->index();
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList()
    {
        Assert::same($this->responseChecker->countCollectionItems($this->orderShopClient->getLastResponse()), 1);
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
