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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\Exception\BadMethodCallException;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    public function __construct(
        private ApiSecurityClientInterface $apiSecurityClient,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->apiSecurityClient->prepareLoginRequest();
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(string $username): void
    {
        $this->apiSecurityClient->setEmail($username);
    }

    /**
     * @When I specify the password as :password
     */
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->apiSecurityClient->setPassword($password);
    }

    /**
     * @When I log in
     */
    public function iLogIn(): void
    {
        $this->apiSecurityClient->call();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->apiSecurityClient->isLoggedIn(), 'Admin should be logged in, but they are not.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        try {
            Assert::false($this->apiSecurityClient->isLoggedIn(), 'Admin should not be logged in, but they are.');
        } catch (BadMethodCallException) {
            Assert::same($this->sharedStorage->get('last_response')->getStatusCode(), 401, 'Admin should not be logged in, but they are.');
        }
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::same($this->apiSecurityClient->getErrorMessage(), 'Invalid credentials.');
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
