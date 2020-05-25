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
use Symfony\Component\BrowserKit\AbstractBrowser;
use Webmozart\Assert\Assert;

final class RegistrationContext implements Context
{
    /** @var AbstractBrowser */
    private $client;

    private $content = [];

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    /**
     * @When I want to register a new account
     * @When I want to again register a new account
     */
    public function iWantToRegisterNewAccount(): void
    {
        $this->content = [
            'firstName' => 'First',
            'lastName' => 'Last',
            'email' => 'example@example.com',
            'password' => 'example',
        ];
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
     * @When I confirm this password
     */
    public function iConfirmThisPassword(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->client->request(
            'POST',
            '/new-api/register',
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/ld+json'],
            json_encode($this->content, \JSON_THROW_ON_ERROR)
        );
        $this->content = [];
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

    private function assertFieldValidationMessage(string $path, string $message): void
    {
        $decodedResponse = json_decode($this->client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        Assert::keyExists($decodedResponse, 'violations');
        Assert::oneOf(
            ['propertyPath' => $path, 'message' => $message],
            $decodedResponse['violations']
        );
    }
}
