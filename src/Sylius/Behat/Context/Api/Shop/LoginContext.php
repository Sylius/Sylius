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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Exception\BadMethodCallException;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    private ?RequestInterface $request = null;

    public function __construct(
        private ApiSecurityClientInterface $apiSecurityClient,
        private ApiClientInterface $client,
        private IriConverterInterface $iriConverter,
        private AbstractBrowser $shopAuthenticationTokenClient,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
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
            sprintf('%s/shop/customers/token', $this->apiUrlPrefix),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $email, 'password' => 'sylius']),
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
        $this->request = $this->requestFactory->create('shop', 'customers/reset-password', 'Bearer');
    }

    /**
     * @When I reset password for email :email in :locale locale
     */
    public function iResetPasswordForEmailInLocale(string $email, LocaleInterface $locale): void
    {
        $this->iWantToResetPassword();
        $this->iSpecifyTheEmail($email);
        $this->sharedStorage->set('current_locale_code', $locale->getCode());
        $this->iResetIt();
    }

    /**
     * @When /^I follow link on (my) email to reset my password$/
     */
    public function iFollowLinkOnMyEmailToResetPassword(ShopUserInterface $user): void
    {
        $this->request = $this->requestFactory->custom(
            sprintf('%s/shop/customers/reset-password/%s', $this->apiUrlPrefix, $user->getPasswordResetToken()),
            HttpRequest::METHOD_PATCH,
        );
    }

    /**
     * @When I reset it
     * @When I try to reset it
     */
    public function iResetIt(): void
    {
        $this->client->executeCustomRequest($this->request);
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
        try {
            $isLoggedIn = $this->apiSecurityClient->isLoggedIn();
        } catch (BadMethodCallException) {
            /** @var ShopUserInterface $shopUser */
            $shopUser = $this->sharedStorage->get('user');
            $this->client->show(Resources::CUSTOMERS, (string) $shopUser->getCustomer()->getId());

            $isLoggedIn = $this->client->getLastResponse()->getStatusCode() !== Response::HTTP_UNAUTHORIZED;
        }

        Assert::false($isLoggedIn, 'Shop user should not be logged in, but they are.');
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
        Assert::same($this->client->getLastResponse()->getStatusCode(), 202);
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
                'customer',
            ),
            $this->iriConverter->getIriFromResource($customer),
        );
    }

    /**
     * @Then I should not be able to change my password again with the same token
     */
    public function iShouldNotBeAbleToChangeMyPasswordAgainWithTheSameToken(): void
    {
        $response = $this->client->executeCustomRequest($this->request);
        Assert::same($response->getStatusCode(), 422);
        Assert::same($this->responseChecker->getError($response), 'token: Password reset token itotallyforgotmypassword is invalid.');
    }

    /**
     * @Then I should not be able to change my password with this token
     */
    public function iShouldNotBeAbleToChangeMyPasswordWithThisToken(): void
    {
        $response = $this->client->getLastResponse();
        Assert::same($response->getStatusCode(), 422);
        Assert::same($this->responseChecker->getError($response), 'token: Password reset token has expired.');
    }
}
