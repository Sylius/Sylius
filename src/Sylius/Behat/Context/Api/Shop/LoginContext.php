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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    /** @var ApiSecurityClientInterface */
    private $apiSecurityClient;

    /** @var ApiClientInterface */
    private $apiClient;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var AbstractBrowser */
    private $shopAuthenticationTokenClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var Request|null */
    private $request;

    public function __construct(
        ApiSecurityClientInterface $apiSecurityClient,
        ApiClientInterface $apiClient,
        IriConverterInterface $iriConverter,
        AbstractBrowser $shopAuthenticationTokenClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->apiSecurityClient = $apiSecurityClient;
        $this->apiClient = $apiClient;
        $this->iriConverter = $iriConverter;
        $this->shopAuthenticationTokenClient = $shopAuthenticationTokenClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is the visitor
     */
    public function iAmAVisitor(): void
    {
        // Intentionally left blank;
    }

    /**
     * @When I log in with the email :email
     */
    public function iLogInWithTheEmail(string $email): void
    {
        $this->shopAuthenticationTokenClient->request(
            'POST',
            '/api/v2/shop/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $email, 'password' => 'sylius'])
        );

        $response = $this->shopAuthenticationTokenClient->getResponse();
        $content = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        Assert::keyExists($content, 'token', 'Token not found.');
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->apiSecurityClient->prepareLoginRequest();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->request = Request::create('shop', 'reset-password-requests', 'Bearer');
    }

    /**
     * @When I reset password for email :email in :locale locale
     */
    public function iResetPasswordForEmailInLocale(string $email, LocaleInterface $locale): void
    {
        $this->iWantToResetPassword();
        $this->iSpecifyTheEmail($email);
        $this->addLocale($this->iriConverter->getIriFromItem($locale));
        $this->iResetIt();
    }

    /**
     * @When /^I follow link on (my) email to reset my password$/
     */
    public function iFollowLinkOnMyEmailToResetPassword(ShopUserInterface $user): void
    {
        $this->request = Request::custom(
            sprintf('api/v2/shop/reset-password-requests/%s', $user->getPasswordResetToken()),
            HttpRequest::METHOD_PATCH
        );
    }

    /**
     * @When I reset it
     * @When I try to reset it
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
        $this->apiSecurityClient->setEmail($username);
    }

    /**
     * @When I specify customer email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail(string $email = ''): void
    {
        $this->request->updateContent(['email' => $email]);
    }

    /**
     * @When I specify my new password as :password
     * @When I do not specify my new password
     */
    public function iSpecifyMyNewPassword(?string $password = null): void
    {
        $this->request->updateContent(['newPassword' => $password]);
    }

    /**
     * @When I confirm my new password as :password
     * @When I do not confirm my new password
     */
    public function iConfirmMyNewPassword(?string $password = null): void
    {
        $this->request->updateContent(['confirmNewPassword' => $password]);
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
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->apiSecurityClient->call();
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->apiSecurityClient->prepareLoginRequest();
        $this->apiSecurityClient->setEmail($email);
        $this->apiSecurityClient->setPassword($password);
        $this->apiSecurityClient->call();
    }

    /**
     * @When I log out
     * @When the customer logged out
     */
    public function iLogOut()
    {
        $this->apiSecurityClient->logOut();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->apiSecurityClient->isLoggedIn(), 'Shop user should be logged in, but they are not.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->apiSecurityClient->isLoggedIn(), 'Shop user should not be logged in, but they are.');
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::same($this->apiSecurityClient->getErrorMessage(), 'Invalid credentials.');
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     * @Then I should be notified that my password has been successfully reset
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionWasSent(): void
    {
        Assert::same($this->apiClient->getLastResponse()->getStatusCode(), 202);
    }

    /**
     * @Then I should be able to log in as :email with :password password
     * @Then the customer should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->iLogInAsWithPassword($email, $password);

        $this->iShouldBeLoggedIn();
    }

    /**
     * @Then I should not be able to log in as :email with :password password
     */
    public function iShouldNotBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->iLogInAsWithPassword($email, $password);

        $this->iShouldNotBeLoggedIn();
    }

    /**
     * @Then I should see who I am
     */
    public function iShouldSeeWhoIAm(): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->sharedStorage->get('customer');

        Assert::same(
            $this->responseChecker->getValue(
                $this->shopAuthenticationTokenClient->getResponse(),
                'customer'
            ),
            $this->iriConverter->getIriFromItem($customer)
        );
    }

    private function addLocale(string $locale): void
    {
        $this->request->updateContent(['locale' => $locale]);
    }
}
