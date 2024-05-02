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
use Sylius\Behat\Client\ApiPlatformClient;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Context\Setup\ShopSecurityContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    private ?string $verificationToken = '';

    public function __construct(
        private ApiClientInterface $client,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker,
        private RegistrationContext $registrationContext,
        private LoginContext $loginContext,
        private ShopSecurityContext $shopApiSecurityContext,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When I want to modify my profile
     */
    public function iWantToModifyMyProfile(): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');
        $customer = $shopUser->getCustomer();

        $this->client->buildUpdateRequest(Resources::CUSTOMERS, (string) $customer->getId());
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

        Assert::isInstanceOf($this->client, ApiPlatformClient::class);
        $this->client->buildCustomUpdateRequest(Resources::CUSTOMERS, (string) $customer->getId(), 'password');
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName(string $firstName = ''): void
    {
        $this->client->addRequestData('firstName', $firstName);
    }

    /**
     * @When I specify the :firstOrLast name as null value
     */
    public function iSpecifyTheFirstOrLastNameAsNull(string $firstOrLast): void
    {
        $this->client->addRequestData($firstOrLast . 'Name', null);
    }

    /**
     * @When I specify the gender as a wrong value
     */
    public function iSpecifyTheFirstNameAsWrongValue(): void
    {
        $this->client->addRequestData('gender', 'wrong_value');
    }

    /**
     * @When I specify the phone number as huge value
     */
    public function iSpecifyThePhoneNumberAsHugeValue(): void
    {
        $this->client->addRequestData('phoneNumber', str_repeat('1', 256));
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName(string $lastName = ''): void
    {
        $this->client->addRequestData('lastName', $lastName);
    }

    /**
     * @When I specify the customer email as :email
     * @When I remove the customer email
     */
    public function iSpecifyCustomerTheEmail(string $email = ''): void
    {
        $this->client->addRequestData('email', $email);
    }

    /**
     * @When I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs(string $password): void
    {
        $this->client->addRequestData('currentPassword', $password);
    }

    /**
     * @When I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs(string $password): void
    {
        $this->client->addRequestData('newPassword', $password);
    }

    /**
     * @When I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs(string $password): void
    {
        $this->client->addRequestData('confirmNewPassword', $password);
    }

    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo(string $oldPassword, string $newPassword): void
    {
        $this->client->setRequestData([
            'currentPassword' => $oldPassword,
            'newPassword' => $newPassword,
            'confirmNewPassword' => $newPassword,
        ]);
    }

    /**
     * @When I subscribe to the newsletter
     */
    public function iSubscribeToTheNewsletter(): void
    {
        $this->client->addRequestData('subscribedToNewsletter', true);
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter(): void
    {
        $response = $this->client->getLastResponse();

        Assert::true($this->responseChecker->getValue($response, 'subscribedToNewsletter'));
    }

    /**
     * @When /^(I) try to verify my account using the link from this email$/
     */
    public function iTryToVerifyMyAccountUsingTheLinkFromEmail(ShopUserInterface $user): void
    {
        $this->verificationToken = $user->getEmailVerificationToken();
        $this->verifyAccount($this->verificationToken);
    }

    /**
     * @When I (try to )verify using :token token
     */
    public function iTryToVerifyUsing(string $token): void
    {
        $this->verifyAccount($token);
    }

    /**
     * @When I resend the verification email
     */
    public function iResendVerificationEmail(): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->sharedStorage->get('user');

        $this->resendVerificationEmail($user->getEmail());
    }

    /**
     * @When I use the verification link from the first email to verify
     */
    public function iUseVerificationLinkFromFirstEmailToVerify(): void
    {
        $token = $this->sharedStorage->get('verification_token');

        $this->verifyAccount($token);
    }

    /**
     * @When I register with email :email and password :password
     */
    public function iRegisterWithEmailAndPassword(string $email, string $password): void
    {
        $this->registerAccount($email, $password);
        $this->loginContext->iLogInAsWithPassword($email, $password);
    }

    /**
     * @Then I should be notified that the verification email has been sent
     */
    public function iShouldBeNotifiedThatTheVerificationEmailHasBeenSent(): void
    {
        Assert::same($this->client->getLastResponse()->getStatusCode(), 202);
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

        $response = $this->client->show(Resources::CUSTOMERS, (string) $shopUser->getCustomer()->getId());

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

        $response = $this->client->show(Resources::CUSTOMERS, (string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'fullName', $name));
    }

    /**
     * @Then my gender should still be :gender
     */
    public function myGenderShouldBe(string $gender): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');

        $response = $this->client->show(Resources::CUSTOMERS, (string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'gender', $gender));
    }

    /**
     * @Then my phone number should still be :phoneNumber
     */
    public function myPhoneNumberShouldBe(string $phoneNumber): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->sharedStorage->get('user');

        $response = $this->client->show(Resources::CUSTOMERS, (string) $shopUser->getCustomer()->getId());

        Assert::true($this->responseChecker->hasValue($response, 'phoneNumber', $phoneNumber));
    }

    /**
     * @Then I should be notified that the first name is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'First name must be at least 2 characters long.',
        ));
    }

    /**
     * @Then I should be (also) notified that the :firstOrLast name needs to be provided
     */
    public function iShouldBeNotifiedThatFirstOrLastNameNeedsToBeProvided(string $firstOrLast): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            sprintf('Please enter your %s name.', $firstOrLast),
        ));
    }

    /**
     * @Then I should be notified that my gender is invalid
     */
    public function iShouldBeNotifiedThatGenderIsInvalid(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'The value you selected is not a valid choice.',
        ));
    }

    /**
     * @Then I should be notified that the phone number is too long
     */
    public function iShouldBeNotifiedThatThePhoneNumberIsTooLong(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'Phone number must not be longer than 255 characters.',
        ));
    }

    /**
     * @Then I should be notified that the last name is required
     */
    public function iShouldBeNotifiedThatLastNameIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'Last name must be at least 2 characters long.',
        ));
    }

    /**
     * @Then I should be notified that the email is required
     */
    public function iShouldBeNotifiedThatEmailIsRequired(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'Please enter your email.',
        ));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'This email is already used.',
        ));
    }

    /**
     * @Then I should be notified that the email is invalid
     */
    public function iShouldBeNotifiedThatEmailIsInvalid(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'This email is invalid.',
        ));
    }

    /**
     * @Then I should be notified that the verification token is invalid
     */
    public function iShouldBeNotifiedThatTheVerificationTokenIsInvalid(): void
    {
        $this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            sprintf('There is no shop user with %s email verification token.', $this->verificationToken),
        );
    }

    /**
     * @When I browse my orders
     */
    public function iBrowseMyOrders(): void
    {
        $this->client->index(Resources::ORDERS);
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
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::ORDERS)), 1);
    }

    /**
     * @Then this order should have :orderNumber number
     */
    public function thisOrderShouldHaveNumber(string $orderNumber): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'number',
                $orderNumber,
            ),
        );
    }

    /**
     * @Then I should be notified that the verification was successful
     */
    public function iShouldBeNotifiedThatTheVerificationWasSuccessful(): void
    {
        $this->responseChecker->isCreationSuccessful($this->client->getLastResponse());
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     * @Then I should be notified that new account has been successfully created
     * @Then I should be notified that my account has been created and the verification email has been sent
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyChanged(): void
    {
        $response = $this->client->getLastResponse();

        Assert::same(
            $response->getStatusCode(),
            204,
            $response->getContent(),
        );
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne(): void
    {
        Assert::same($this->client->getLastResponse()->getStatusCode(), 422);

        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Provided password is different than the current one.',
        );
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch(): void
    {
        Assert::same($this->client->getLastResponse()->getStatusCode(), 422);

        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'newPassword: The entered passwords don\'t match',
        );
    }

    /**
     * @Then /^I should be notified that the ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe(string $elementName, string $validationMessage): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage),
        );
    }

    /**
     * @Then my account should be verified
     */
    public function myAccountShouldBeVerified(): void
    {
        $response = $this->getResponseWithAccountData();

        Assert::true($this->responseChecker->getResponseContent($response)['user']['verified']);
    }

    /**
     * @Then /^(?:my|his|her) account should not be verified$/
     */
    public function myAccountShouldNotBeVerified(): void
    {
        $response = $this->getResponseWithAccountData();

        Assert::false($this->responseChecker->getResponseContent($response)['user']['verified']);
    }

    /**
     * @Then I should not be able to resend the verification email
     */
    public function iShouldBeUnableToResendVerificationEmail(): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->sharedStorage->get('user');

        $this->resendVerificationEmail($user->getEmail());

        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            \sprintf('Account with email %s is currently verified.', $user->getEmail()),
            'Validation message is different then expected.',
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

    private function verifyAccount(string $token): void
    {
        $request = $this->requestFactory->custom(
            \sprintf('%s/shop/customers/verify/%s', $this->apiUrlPrefix, $token),
            HttpRequest::METHOD_PATCH,
        );

        $this->client->executeCustomRequest($request);
    }

    private function registerAccount(?string $email = 'example@example.com', ?string $password = 'example'): void
    {
        $request = $this->requestFactory->create('shop', Resources::CUSTOMERS, '');

        $request->setContent([
            'firstName' => 'First',
            'lastName' => 'Last',
            'email' => $email,
            'password' => $password,
        ]);

        $this->client->executeCustomRequest($request);
    }

    private function resendVerificationEmail(string $email): void
    {
        $request = $this->requestFactory->create('shop', 'customers/verify', 'Bearer');

        $request->setContent(['email' => $email]);

        $this->client->executeCustomRequest($request);
    }

    private function getResponseWithAccountData(): Response
    {
        /** @var ShopUserInterface $user */
        $user = $this->sharedStorage->get('user');
        $this->loginContext->iLogInAsWithPassword($user->getEmail(), 'sylius');

        return $this->client->show(Resources::CUSTOMERS, (string) $user->getCustomer()->getId());
    }
}
