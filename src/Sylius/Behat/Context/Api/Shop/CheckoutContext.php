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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    /** @var AbstractBrowser */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var string[] */
    private $content = [];

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        AbstractBrowser $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I am at the checkout addressing step
     */
    public function iAmAtTheCheckoutAddressingStep(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I specify the email as :email
     */
    public function iSpecifyTheEmailAs(string $email): void
    {
        $this->content['email'] = $email;
    }

    /**
     * @When /^I specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address): void
    {
        $this->content['billingAddress']['city'] = $address->getCity();
        $this->content['billingAddress']['street'] = $address->getStreet();
        $this->content['billingAddress']['postcode'] = $address->getPostcode();
        $this->content['billingAddress']['countryCode'] = $address->getCountryCode();
        $this->content['billingAddress']['firstName'] = $address->getFirstName();
        $this->content['billingAddress']['lastName'] = $address->getLastName();
    }


    /**
     * @When /^I complete addressing step with email "([^"]+)" and ("[^"]+" based billing address)$/
     */
    public function iCompleteAddressingStepWithEmail(string $email, AddressInterface $address): void
    {
        $this->addressOrder([
            'email' => $email,
            'billingAddress' => [
                'city' => $address->getCity(),
                'street' => $address->getStreet(),
                'postcode' => $address->getPostcode(),
                'countryCode' => $address->getCountryCode(),
                'firstName' => $address->getFirstName(),
                'lastName' => $address->getLastName(),
            ],
        ]);
    }

    /**
     * @When I proceed with :shippingMethod shipping method
     */
    public function iProceededWithShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/select-shipping-methods', $cartToken),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'],
            json_encode([
                'shipmentIdentifier' => 0,
                'shippingMethod' => $shippingMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();
    }

    /**
     * @When I choose :paymentMethod payment method
     */
    public function iChoosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/select-payment-methods', $cartToken),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'],
            json_encode([
                'paymentIdentifier' => 0,
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder(): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/complete', $cartToken),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'],
            json_encode([], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @When I complete the addressing step
     */
    public function iCompleteTheAddressingStep(): void
    {
        $this->addressOrder($this->content);

        $this->content = [];
    }

    /**
     * @Then I should see the thank you page
     */
    public function iShouldSeeTheThankYouPage(): void
    {
        /** @var Response $response */
        $response = $this->client->getResponse();

        $value = $this->responseChecker->getValue($response, 'checkoutState');

        Assert::same($value, OrderCheckoutStates::STATE_COMPLETED);
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep(): void
    {
        $response = $this->client->getResponse();

        $value = $this->responseChecker->getValue($response, 'checkoutState');

        Assert::same($value, OrderCheckoutStates::STATE_ADDRESSED);
    }

    private function addressOrder(array $content): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/address', $cartToken),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'],
            json_encode($content, \JSON_THROW_ON_ERROR)
        );
    }
}
