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
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Sylius\Behat\Client\Request;
use Webmozart\Assert\Assert;
use \Symfony\Component\HttpFoundation\Request as HTTPRequest;

final class LoginContext implements Context
{
    /** @var ApiSecurityClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $apiClient;

    /** @var Request */
    private $request;

    public function __construct(ApiSecurityClientInterface $client, ApiClientInterface $apiClient)
    {
        $this->client = $client;
        $this->apiClient = $apiClient;
    }

    /**
     * @Given there is the visitor
     */
    public function iAmAVisitor(): void
    {
        // Intentionally left blank;
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->client->prepareLoginRequest();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->request = Request::create('shop', 'password-reset-request', 'Bearer');
    }

    /**
     * @When I reset it
     */
    public function iResetIt(): void
    {
        $this->apiClient->executeCustomRequest($this->request);
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(string $username): void
    {
        $this->client->setEmail($username);
    }

    /**
     * @When I specify the email as :email
     */
    public function iSpecifyTheEmail(string $email): void
    {
        $this->request->setContent(['email' => $email]);
    }

    /**
     * @When I specify the password as :password
     */
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->client->setPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->client->call();
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->client->prepareLoginRequest();
        $this->client->setEmail($email);
        $this->client->setPassword($password);
        $this->client->call();
    }

    /**
     * @When I log out
     * @When the customer logged out
     */
    public function iLogOut()
    {
        $this->client->logOut();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->client->isLoggedIn(), 'Shop user should be logged in, but they are not.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->client->isLoggedIn(), 'Shop user should not be logged in, but they are.');
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::same($this->client->getErrorMessage(), 'Invalid credentials.');
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionWasSent(): void
    {
        $response = $this->apiClient->getLastResponse();
        Assert::same($response->getStatusCode(), 202);
    }
}
