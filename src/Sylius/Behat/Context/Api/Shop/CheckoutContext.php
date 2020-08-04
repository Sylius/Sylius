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
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    /** @var AbstractBrowser */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string[] */
    private $content = [];

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
    public function iSpecifyTheEmailAs(?string $email): void
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
     * @When /^I specify the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address): void
    {
        $this->content['shippingAddress']['city'] = $address->getCity();
        $this->content['shippingAddress']['street'] = $address->getStreet();
        $this->content['shippingAddress']['postcode'] = $address->getPostcode();
        $this->content['shippingAddress']['countryCode'] = $address->getCountryCode();
        $this->content['shippingAddress']['firstName'] = $address->getFirstName();
        $this->content['shippingAddress']['lastName'] = $address->getLastName();
    }

    /**
     * @When /^I specified the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifiedTheBillingAddressAs(AddressInterface $address): void
    {
        $this->iSpecifyTheEmailAs(null);
        $this->iSpecifyTheBillingAddressAs($address);
        $this->iCompleteTheAddressingStep();
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
        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/select-shipping-methods', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'shipmentIdentifier' => 0,
                'shippingMethod' => $shippingMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @When I proceed with :shippingMethod shipping method and :paymentMethod payment
     */
    public function iProceedOrderWithShippingMethodAndPayment(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod
    ): void {
        $this->iProceededWithShippingMethod($shippingMethod);
        $this->iChoosePaymentMethod($paymentMethod);
    }

    /**
     * @When I provide additional note like :notes
     */
    public function iProvideAdditionalNotesLike(string $notes): void
    {
        $this->content['additionalNote'] = $notes;

        $this->sharedStorage->set('additional_note', $notes);
    }

    /**
     * @When I choose :paymentMethod payment method
     */
    public function iChoosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/select-payment-methods', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'paymentIdentifier' => 0,
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder(): void
    {
        $notes = isset($this->content['additionalNote']) ? $this->content['additionalNote'] : null;

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/complete', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'notes' => $notes,
            ], \JSON_THROW_ON_ERROR)
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
        $value = $this->responseChecker->getValue($this->client->getResponse(), 'checkoutState');

        Assert::same($value, OrderCheckoutStates::STATE_COMPLETED);
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep(): void
    {
        $value = $this->responseChecker->getValue($this->client->getResponse(), 'checkoutState');

        Assert::same($value, OrderCheckoutStates::STATE_ADDRESSED);
    }

    private function addressOrder(array $content): void
    {
        if (!array_key_exists('email', $content)) {
            $content['email'] = null;
        }

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/orders/%s/address', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode($content, \JSON_THROW_ON_ERROR)
        );
    }

    private function getHeaders(array $headers = []): array
    {
        if (empty($headers)) {
            $headers = ['HTTP_ACCEPT' => 'application/ld+json', 'CONTENT_TYPE' => 'application/merge-patch+json'];
        }

        if ($this->sharedStorage->has('token')) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $this->sharedStorage->get('token');
        }

        return $headers;
    }
}
