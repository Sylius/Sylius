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
use Webmozart\Assert\Assert;

final class ContactContext implements Context
{
    private array $content = [];

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I want to request contact
     * @When I do not specify the email
     * @When I do not specify the message
     */
    public function iWantToRequestContact(): void
    {
        //intentionally left empty
    }

    /**
     * @When I specify the message as :message
     */
    public function iSpecifyTheMessage(string $message): void
    {
        $this->content['message'] = $message;
    }

    /**
     * @When I specify the email as :email
     */
    public function iSpecifyTheEmail($email): void
    {
        $this->content['email'] = $email;
    }

    /**
     * @When I( try to) send it
     */
    public function iSendIt(): void
    {
        $request = $this->requestFactory->create(
            'shop',
            Resources::CONTACT,
            'Authorization',
            $this->client->getToken(),
        );

        $request->setContent($this->content);

        $this->client->request($request);
    }

    /**
     * @Then I should be notified that the contact request has been submitted successfully
     */
    public function iShouldBeNotifiedThatTheContactRequestHasBeenSubmittedSuccessfully(): void
    {
        $response = $this->client->getLastResponse();

        Assert::same($response->getStatusCode(), 202);
    }

    /**
     * @Then I should be notified that the email is invalid
     */
    public function iShouldBeNotifiedThatEmailIsInvalid(): void
    {
        $response = $this->client->getLastResponse();

        Assert::same(
            $this->responseChecker->getError($response),
            'email: The provided email is invalid.',
        );
    }

    /**
     * @Then /^I should be notified that the (email|message) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        $response = $this->client->getLastResponse();

        Assert::same(
            $this->responseChecker->getError($response),
            sprintf('%s: This value should not be blank.', $element),
        );
    }
}
