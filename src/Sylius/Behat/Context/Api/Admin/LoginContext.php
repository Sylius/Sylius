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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    /** @var ApiSecurityClientInterface */
    private $client;

    public function __construct(ApiSecurityClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->client->prepareLoginRequest();
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(string $username): void
    {
        $this->client->setEmail($username);
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
     */
    public function iLogIn(): void
    {
        $this->client->call();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->client->isLoggedIn(), 'Admin should be logged in, but they are not.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->client->isLoggedIn(), 'Admin should not be logged in, but they are.');
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::same($this->client->getErrorMessage(), 'Invalid credentials.');
    }

    /**
     * @Then I should be able to log in as :username authenticated by :password password
     */
    public function iShouldBeAbleToLogInAsAuthenticatedByPassword(string $username, string $password): void
    {
        $this->logIn($username, $password);
        $this->iShouldBeLoggedIn();
    }

    /**
     * @Then I should not be able to log in as :username authenticated by :password password
     */
    public function iShouldNotBeAbleToLogInAsAuthenticatedByPassword(string $username, string $password): void
    {
        $this->logIn($username, $password);
        $this->iShouldNotBeLoggedIn();
    }

    private function logIn(string $username, string $password): void
    {
        $this->iWantToLogIn();
        $this->iSpecifyTheUsername($username);
        $this->iSpecifyThePasswordAs($password);
        $this->iLogIn();
    }
}
