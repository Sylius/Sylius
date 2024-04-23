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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class RegistrationContext implements Context
{
    private array $content = [];

    public function __construct(
        private ApiClientInterface $shopClient,
        private LoginContext $loginContext,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
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
     * @When I specify the :firstOrLast name as too long value
     */
    public function iSpecifyTheFirstOrLastNameAsTooLongValue(string $firstOrLast): void
    {
        $this->content[$firstOrLast . 'Name'] = str_repeat('a', 256);
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

        $request = $this->requestFactory->custom(
            \sprintf('%s/shop/customers/verify/%s', $this->apiUrlPrefix, $token),
            HttpRequest::METHOD_PATCH,
        );
        $this->shopClient->executeCustomRequest($request);
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
     * @When I register with email :email and password :password in the :localeCode locale
     */
    public function iRegisterWithEmailAndPassword(string $email, string $password, string $localeCode = 'en_US'): void
    {
        $this->sharedStorage->set('current_locale_code', $localeCode);

        $this->fillContent($email, $password);
        $this->iSpecifyTheFirstNameAs('John');
        $this->iSpecifyTheLastNameAs('Doe');
        $this->iRegisterThisAccount();
        $this->loginContext->iLogInAsWithPassword($email, $password);
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegisterThisAccount(): void
    {
        $request = $this->requestFactory->create('shop', Resources::CUSTOMERS, '');
        $request->setContent($this->content);

        $this->shopClient->executeCustomRequest($request);

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
        Assert::same($this->shopClient->getLastResponse()->getStatusCode(), 204);
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
            'Request does not have the following required fields specified: ' . implode(', ', $fields) . '.',
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
     * @Then I should be notified that the :firstOrLast name is too long
     */
    public function iShouldBeNotifiedThatTheFirstOrLastNameIsTooLong(string $firstOrLast): void
    {
        $this->assertFieldValidationMessage($firstOrLast . 'Name', sprintf('%s name must not be longer than 255 characters.', ucfirst($firstOrLast)));
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
     * @Then I should be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter(): void
    {
        $customer = $this->sharedStorage->get('customer');

        $response = $this->shopClient->show(Resources::CUSTOMERS, (string) $customer->getId());

        Assert::true($this->responseChecker->getResponseContent($response)['subscribedToNewsletter']);
    }

    /**
     * @Then I should be on my account dashboard
     * @Then I should be on registration thank you page
     */
    public function intentionallyLeftBlank(): void
    {
    }

    private function assertFieldValidationMessage(string $path, string $message): void
    {
        $decodedResponse = $this->getResponseContent();

        Assert::keyExists($decodedResponse, 'violations');
        Assert::same(
            $decodedResponse['violations'][0],
            ['propertyPath' => $path, 'message' => $message, 'code' => $decodedResponse['violations'][0]['code']],
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
        return json_decode($this->shopClient->getLastResponse()->getContent(), true);
    }

    private function convertElementsToCamelCase(array $fields): array
    {
        foreach ($fields as $key => $field) {
            $fields[$key] = lcfirst(str_replace(' ', '', ucwords($field)));
        }

        return $fields;
    }
}
