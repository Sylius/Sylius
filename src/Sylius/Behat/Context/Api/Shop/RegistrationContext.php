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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Webmozart\Assert\Assert;

final class RegistrationContext implements Context
{
    /** @var AbstractBrowser */
    private $client;

    /** @var ApiClientInterface */
    private $customerClient;

    /** @var LoginContext */
    private $loginContext;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    private $content = [];

    public function __construct(
        AbstractBrowser $client,
        ApiClientInterface $customerClient,
        LoginContext $loginContext,
        SharedStorageInterface $sharedStorage,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->customerClient = $customerClient;
        $this->loginContext = $loginContext;
        $this->sharedStorage = $sharedStorage;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I want to register a new account
     * @When I want to again register a new account
     */
    public function iWantToRegisterNewAccount(): void
    {
        $this->fillContent();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I do not specify the first name
     */
    public function iSpecifyTheFirstNameAs(string $firstName = ''): void
    {
        $this->content['firstName'] = $firstName;
    }

    /**
     * @When I specify the last name as :lastName
     * @When I do not specify the last name
     */
    public function iSpecifyTheLastNameAs(string $lastName = ''): void
    {
        $this->content['lastName'] = $lastName;
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmailAs(string $email = ''): void
    {
        $this->content['email'] = $email;
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs(string $password = ''): void
    {
        $this->content['password'] = $password;
    }

    /**
     * @When I specify the phone number as :phoneNumber
     */
    public function iSpecifyThePhoneNumberAs(string $phoneNumber): void
    {
        $this->content['phoneNumber'] = $phoneNumber;
    }

    /**
     * @When I subscribe to the newsletter
     */
    public function iSubscribeToTheNewsletter(): void
    {
        $this->content['subscribedToNewsletter'] = true;
    }

    /**
     * @When I verify my account using link sent to :customer
     */
    public function iVerifyMyAccountUsingLink(CustomerInterface $customer): void
    {
        $this->sharedStorage->set('customer', $customer);

        $token = $customer->getUser()->getEmailVerificationToken();

        $this->client->request(
            'PATCH',
            \sprintf('/api/v2/shop/account-verification-requests/%s', $token),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'],
            json_encode([], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @When I confirm this password
     */
    public function iConfirmThisPassword(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I register with email :email and password :password
     */
    public function iRegisterWithEmailAndPassword(string $email, string $password): void
    {
        $this->fillContent($email, $password);
        $this->iRegisterThisAccount();
        $this->loginContext->iLogInAsWithPassword($email, $password);
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->client->request(
            'POST',
            '/api/v2/shop/customers',
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/ld+json'],
            json_encode($this->content, \JSON_THROW_ON_ERROR)
        );
        $this->content = [];
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginContext->iLogInAsWithPassword($email, $password);
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->loginContext->iLogOut();
    }

    /**
     * @Then I should be notified that new account has been successfully created
     */
    public function iShouldBeNotifiedThatNewAccountHasBeenSuccessfullyCreated(): void
    {
        Assert::same($this->client->getResponse()->getStatusCode(), 204);
    }

    /**
     * @Then I should be notified that the first name is required
     */
    public function iShouldBeNotifiedThatTheFirstNameIsRequired(): void
    {
        $this->assertFieldValidationMessage('firstName', 'Please enter your first name.');
    }

    /**
     * @Then /^I should be notified that the "([^"]+)" and "([^"]+)" have to be provided$/
     */
    public function iShouldBeNotifiedThatFieldHaveToBeProvided(string ...$fields): void
    {
        $fields = $this->convertElementsToCamelCase($fields);
        $content = $this->getResponseContent();

        Assert::same(
            $content['message'],
            'Request does not have the following required fields specified: ' . implode(', ', $fields) . '.'
        );
        Assert::same($content['code'], 400);
    }

    /**
     * @Then I should be notified that the last name is required
     */
    public function iShouldBeNotifiedThatTheLastNameIsRequired(): void
    {
        $this->assertFieldValidationMessage('lastName', 'Please enter your last name.');
    }

    /**
     * @Then I should be notified that the password is required
     */
    public function iShouldBeNotifiedThatThePasswordIsRequired(): void
    {
        $this->assertFieldValidationMessage('password', 'Please enter your password.');
    }

    /**
     * @Then I should be notified that the email is required
     */
    public function iShouldBeNotifiedThatTheEmailIsRequired(): void
    {
        $this->assertFieldValidationMessage('email', 'Please enter your email.');
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        $this->assertFieldValidationMessage('email', 'This email is already used.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter(): void
    {
        $customer = $this->sharedStorage->get('customer');

        $response = $this->customerClient->show((string) $customer->getId());

        Assert::true($this->responseChecker->getResponseContent($response)['subscribedToNewsletter']);
    }

    private function assertFieldValidationMessage(string $path, string $message): void
    {
        $decodedResponse = $this->getResponseContent();

        Assert::keyExists($decodedResponse, 'violations');
        Assert::same(
            $decodedResponse['violations'][0],
            ['propertyPath' => $path, 'message' => $message, 'code' => $decodedResponse['violations'][0]['code']]
        );
    }

    private function fillContent(?string $email = 'example@example.com', ?string $password = 'example'): void
    {
        $this->content = [
            'email' => $email,
            'password' => $password,
        ];
    }

    private function getResponseContent(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    private function convertElementsToCamelCase(array $fields): array
    {
        foreach ($fields as $key => $field) {
            $fields[$key] = lcfirst(str_replace(' ', '', ucwords($field)));
        }

        return $fields;
    }
}
