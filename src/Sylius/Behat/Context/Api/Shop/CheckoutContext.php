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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    public const CHECKOUT_STATE_TYPES = [
        'address' => OrderCheckoutStates::STATE_ADDRESSED,
        'shipping method' => OrderCheckoutStates::STATE_SHIPPING_SELECTED,
        'payment' => OrderCheckoutStates::STATE_PAYMENT_SELECTED,
    ];

    /** @var AbstractBrowser */
    private $client;

    /** @var ApiClientInterface */
    private $orderClient;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var RepositoryInterface */
    private $shippingMethodRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $paymentMethodRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string[] */
    private $content = [];

    public function __construct(
        AbstractBrowser $client,
        ApiClientInterface $orderClient,
        IriConverterInterface $iriConverter,
        ResponseCheckerInterface $responseChecker,
        RepositoryInterface $shippingMethodRepository,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $paymentMethodRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->orderClient = $orderClient;
        $this->iriConverter = $iriConverter;
        $this->responseChecker = $responseChecker;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I proceeded with :shippingMethod shipping method and :paymentMethod payment
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
     * @Given I am at the checkout addressing step
     * @When I complete the payment step
     * @When I complete the shipping step
     * @When I go to the checkout addressing step
     * @Then there should be information about no available shipping methods
     * @Then I should be informed that my order cannot be shipped to this address
     */
    public function iAmAtTheCheckoutAddressingStep(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I specify the email as :email
     * @When /^the (?:customer|visitor) specify the email as "([^"]+)"$/
     * @Given /^the (?:customer|visitor) has specified the email as "([^"]+)"$/
     */
    public function iSpecifyTheEmailAs(?string $email): void
    {
        $this->content['email'] = $email;
    }

    /**
     * @When /^I specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^the (?:customer|visitor) specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @Given /^the (?:visitor|customer) has specified (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address): void
    {
        $this->fillAddress('billingAddress', $address);
    }

    /**
     * @When /^I specify the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address): void
    {
        $this->fillAddress('shippingAddress', $address);
    }

    /**
     * @When /^I (do not specify any shipping address) information$/
     */
    public function iDoNotSpecifyAnyShippingAddressInformation(AddressInterface $address): void
    {
        $this->fillAddress('billingAddress', $address);
        $this->fillAddress('shippingAddress', $address);
    }

    /**
     * @When /^I (do not specify any billing address) information$/
     */
    public function iDoNotSpecifyAnyBillingAddressInformation(AddressInterface $address): void
    {
        $this->fillAddress('billingAddress', $address);
    }

    /**
     * @When /^I specified the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I define the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
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
     * @When I complete the addressing step
     * @When I try to complete the addressing step
     * @When /^the (?:customer|visitor) complete the addressing step$/
     * @Given /^the (?:customer|visitor) has completed the addressing step$/
     * @When the visitor try to complete the addressing step in the customer cart
     */
    public function iCompleteTheAddressingStep(): void
    {
        $this->addressOrder($this->content);

        $this->content = [];
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
     * @When I confirm my order
     * @When I try to confirm my order
     * @When /^the (?:visitor|customer) confirm his order$/
     */
    public function iConfirmMyOrder(): void
    {
        $notes = isset($this->content['additionalNote']) ? $this->content['additionalNote'] : null;

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/shop/orders/%s/complete', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'notes' => $notes,
            ], \JSON_THROW_ON_ERROR)
        );

        /** @var Response $response */
        $response = $this->client->getResponse();

        if ($response->getStatusCode() === 400) {
            return;
        }

        $this->sharedStorage->set(
            'order_number',
            $this->responseChecker->getValue($response, 'number')
        );
    }

    /**
     * @When I proceed with :shippingMethod shipping method
     * @When I select :shippingMethod shipping method
     * @When /^the (?:visitor|customer) proceed with ("[^"]+" shipping method)$/
     * @Given /^the (?:visitor|customer) has proceeded ("[^"]+" shipping method)$/
     */
    public function iProceededWithShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf(
                '/new-api/shop/orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                (string) $this->iriConverter->getItemFromIri($this->getCart()['shipments'][0])->getId()
            ),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'shippingMethodCode' => $shippingMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @Then the visitor has no access to proceed with :shippingMethod shipping method in the customer cart
     * @Then the visitor has no access to proceed with :paymentMethod payment in the customer cart
     * @Then the visitor has no access to confirm the customer order
     * @Then the visitor has no access to change product :product quantity to :quantity in the customer cart
     */
    public function theVisitorHasNoProceedWithShippingMethodInTheCustomerCart(): void
    {
        Assert::same($this->getCart()['code'], 404);
    }

    /**
     * @When I complete the shipping step with first shipping method
     */
    public function iCompleteTheShippingStepWithFirstShippingMethod(): void
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([]);

        $this->iProceededWithShippingMethod($shippingMethod);
    }

    /**
     * @When I choose :paymentMethod payment method
     * @When I select :paymentMethod payment method
     * @When /^the (?:customer|visitor) proceed with ("[^"]+" payment)$/
     * @Given /^the (?:customer|visitor) has proceeded ("[^"]+" payment)$/
     */
    public function iChoosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf(
                '/new-api/shop/orders/%s/payments/%s',
                $this->sharedStorage->get('cart_token'),
                (string) $this->iriConverter->getItemFromIri($this->getCart()['payments'][0])->getId()
            ),
            [],
            [],
            $this->getHeaders(),
            json_encode([
                'paymentMethodCode' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @When I proceed through checkout process
     */
    public function iProceedThroughCheckoutProcess(): void
    {
        $this->addressOrder([
            'email' => 'rich@sylius.com',
            'billingAddress' => [
                'city' => 'New York',
                'street' => 'Wall Street',
                'postcode' => '00-001',
                'countryCode' => 'US',
                'firstName' => 'Richy',
                'lastName' => 'Rich',
            ],
        ]);

        $this->iCompleteTheShippingStepWithFirstShippingMethod();

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([]);
        $this->iChoosePaymentMethod($paymentMethod);
    }

    /**
     * @Then I should be on the checkout complete step
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutCompleteStep(): void
    {
        Assert::inArray(
            $this->getCheckoutState(),
            [OrderCheckoutStates::STATE_PAYMENT_SKIPPED, OrderCheckoutStates::STATE_PAYMENT_SELECTED]
        );
    }

    /**
     * @Then I should not be able to select :paymentMethodName payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod(string $paymentMethodName): void
    {
        $paymentMethods = $this->getPossiblePaymentMethods($paymentMethodName);

        Assert::false(array_search($paymentMethodName, array_column($paymentMethods, 'name')));
    }

    /**
     * @Then I should be able to select :paymentMethodName payment method
     */
    public function iShouldBeAbleToSelectPaymentMethod(string $paymentMethodName): void
    {
        $paymentMethods = $this->getPossiblePaymentMethods($paymentMethodName);

        Assert::notFalse(array_search($paymentMethodName, array_column($paymentMethods, 'name')));
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the :choice choice
     */
    public function iShouldHavePaymentMethodAvailableAsTheChoice(string $paymentMethodName, string $choice): void
    {
        $paymentMethods = $this->getPossiblePaymentMethods($paymentMethodName);
        Assert::notEmpty($paymentMethods);

        if ($choice === 'first') {
            Assert::same(reset($paymentMethods)['name'], $paymentMethodName);
        }
        if ($choice === 'last') {
            Assert::same(end($paymentMethods)['name'], $paymentMethodName);
        }
    }

    /**
     * @Then I should be on the checkout payment step
     */
    public function iShouldBeOnTheCheckoutPaymentStep(): void
    {
        Assert::inArray(
            $this->getCheckoutState(),
            [OrderCheckoutStates::STATE_SHIPPING_SELECTED, OrderCheckoutStates::STATE_SHIPPING_SKIPPED]
        );
    }

    /**
     * @Then I should not see any information about payment method
     */
    public function iShouldNotSeeAnyInformationAboutPaymentMethod(): void
    {
        /** @var Response $response */
        $response = $this->client->getResponse();

        Assert::true(empty($this->responseChecker->getResponseContent($response)['payments']));
    }

    /**
     * @Then I should see :shippingMethod shipping method
     */
    public function iShouldSeeShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        Assert::true($this->hasShippingMethod($shippingMethod));
    }

    /**
     * @Then /^I should see (shipping method "[^"]+") with fee ("[^"]+")/
     */
    public function iShouldSeeShippingFee(ShippingMethodInterface $shippingMethod, int $fee): void
    {
        Assert::true($this->hasShippingMethodWithFee($shippingMethod, $fee));
    }

    /**
     * @Then my order's payment method should be :paymentMethod
     */
    public function myOrdersPaymentMethodShouldBe(PaymentMethodInterface $paymentMethod): void
    {
        /** @var Response $response */
        $response = $this->client->getResponse();
        Assert::same(
            $this->responseChecker->getResponseContent($response)['payments'][0]['method']['name'],
            $paymentMethod->getName()
        );
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrdersShippingMethodShouldBe(ShippingMethodInterface $shippingMethod): void
    {
        /** @var Response $response */
        $response = $this->client->getResponse();
        Assert::same(
            $this->responseChecker->getResponseContent($response)['shipments'][0]['method']['translations']['en_US']['name'],
            $shippingMethod->getName()
        );
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep(): void
    {
        Assert::same($this->getCheckoutState(), OrderCheckoutStates::STATE_ADDRESSED);
    }

    /**
     * @Then /^the (?:visitor|customer) should have checkout (address|shipping method|payment) step completed$/
     */
    public function theVisitorShouldHaveCheckoutAddressStepCompleted(string $stepType): void
    {
        Assert::same($this->getCheckoutState(), $this::CHECKOUT_STATE_TYPES[$stepType]);
    }

    /**
     * @Then I should see the thank you page
     * @Then /^the (?:visitor|customer) should see the thank you page$/
     */
    public function iShouldSeeTheThankYouPage(): void
    {
        Assert::same($this->getCheckoutState(), OrderCheckoutStates::STATE_COMPLETED);
    }

    /**
     * @Then I should not see :shippingMethod shipping method
     * @Then I should not be able to select :shippingMethod shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        Assert::false($this->hasShippingMethod($shippingMethod));
    }

    /**
     * @Then I should have :shippingMethod shipping method available as the first choice
     */
    public function iShouldHaveShippingMethodAvailableAsFirstChoice(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethods = $this->getCartShippingMethods($this->getCart());

        Assert::true($shippingMethods[0]['shippingMethod']['code'] === $shippingMethod->getCode());
    }

    /**
     * @Then I should have :shippingMethod shipping method available as the last choice
     */
    public function iShouldHaveShippingMethodAvailableAsLastChoice(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethods = $this->getCartShippingMethods($this->getCart());

        Assert::true(end($shippingMethods)['shippingMethod']['code'] === $shippingMethod->getCode());
    }

    /**
     * @Then /^my order total should be ("[^"]+")$/
     */
    public function myOrderTotalShouldBe(int $total): void
    {
        $responseTotal = $this->responseChecker->getValue($this->client->getResponse(), 'total');
        Assert::same($total, (int) $responseTotal);
    }

    /**
     * @Then /^my order promotion total should be ("[^"]+")$/
     */
    public function myOrderPromotionTotalShouldBe(int $promotionTotal): void
    {
        $responsePromotionTotal = $this->responseChecker->getValue($response = $this->client->getResponse(), 'orderPromotionTotal');

        Assert::same($promotionTotal, $responsePromotionTotal);
    }

    /**
     * @Then /^my tax total should be ("[^"]+")$/
     */
    public function myTaxTotalShouldBe(int $taxTotal): void
    {
        $responseTaxTotal = $this->responseChecker->getValue($this->client->getResponse(), 'taxTotal');

        Assert::same($taxTotal, $responseTaxTotal);
    }

    /**
     * @Then I should have :quantity :productName products in the cart
     */
    public function iShouldHaveProductsInTheCart(int $quantity, string $productName): void
    {
        Assert::true($this->hasProductWithNameAndQuantityInCart($productName, $quantity), sprintf('There is no product %s with quantity %d.', $productName, $quantity));
    }

    /**
     * @Then there should be no discount
     */
    public function thereShouldBeNoDiscount(): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getResponse(), 'orderPromotionTotal'), 0);
    }

    /**
     * @Then there should be no taxes charged
     */
    public function thereShouldBeNoTaxesCharged(): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getResponse(), 'taxTotal'), 0);
    }

    /**
     * @Then my order's locale should be :localeCode
     */
    public function myOrderLocaleShouldBe(string $localeCode): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getResponse(), 'localeCode'), $localeCode);
    }

    /**
     * @Then /^my order shipping should be ("(?:\£|\$)\d+(?:\.\d+)?")$/
     */
    public function myOrderShippingShouldBe(int $price): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getResponse(), 'shippingTotal'), $price);
    }

    /**
     * @Then I should not see shipping total
     */
    public function iShouldNotSeeShippingTotal(): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getResponse(), 'shippingTotal'), 0);
    }

    /**
     * @Then /^I should(?:| also) be notified that the "([^"]+)" and the "([^"]+)" in (shipping|billing) details are required$/
     */
    public function iShouldBeNotifiedThatTheAndTheInShippingDetailsAreRequired(string $firstElement, string $secondElement, string $detailType): void
    {
        /** @var Response|null $response */
        $response = $this->client->getResponse();

        Assert::true($response->getStatusCode() === 400);

        /** @var array|null $violations */
        $violations = $this->responseChecker->getResponseContent($response)['violations'];

        $detailType .= 'Address';

        foreach ([$firstElement, $secondElement] as $element) {
            $violation = $this->getViolation(
                $violations,
                $detailType . '.' . StringInflector::nameToCamelCase($element));
            Assert::same($violation['message'], sprintf('Please enter %s.', $element));
        }
    }

    /**
     * @Then /^I should be informed that (this product) has been disabled$/
     */
    public function iShouldBeInformedThatThisProductHasBeenDisabled(ProductInterface $product): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getResponse(),
            sprintf('This product %s has been disabled.', $product->getName())
        ));
    }

    /**
     * @Then I should not see the thank you page
     */
    public function iShouldNotSeeTheThankYouPage(): void
    {
        Assert::same($this->client->getResponse()->getStatusCode(), 400);
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

    private function addressOrder(array $content): void
    {
        if (!array_key_exists('email', $content)) {
            $content['email'] = null;
        }

        $this->client->request(
            Request::METHOD_PATCH,
            \sprintf('/new-api/shop/orders/%s/address', $this->sharedStorage->get('cart_token')),
            [],
            [],
            $this->getHeaders(),
            json_encode($content, \JSON_THROW_ON_ERROR)
        );
    }

    private function getCart(): array
    {
        $response = $this->orderClient->show($this->sharedStorage->get('cart_token'));

        return $this->responseChecker->getResponseContent($response);
    }

    private function getCheckoutState(): string
    {
        /** @var Response $response */
        $response = $this->client->getResponse();

        return $this->responseChecker->getValue($response, 'checkoutState');
    }

    private function getCartShippingMethods(array $cart): array
    {
        $shipmentIri = $cart['shipments'][0];

        /** @var ShipmentInterface $shipment */
        $shipment = $this->iriConverter->getItemFromIri($shipmentIri);

        $this->client->request(
            Request::METHOD_GET,
            \sprintf('/new-api/shop/orders/%s/shipments/%s/methods', $cart['tokenValue'], $shipment->getId()),
            [],
            [],
            $this->getHeaders(),
            json_encode([], \JSON_THROW_ON_ERROR)
        );

        return $this->responseChecker->getCollection($this->client->getResponse());
    }

    private function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool
    {
        foreach ($this->getCartShippingMethods($this->getCart()) as $cartShippingMethod) {
            if ($cartShippingMethod['shippingMethod']['code'] === $shippingMethod->getCode()) {
                return true;
            }
        }

        return false;
    }

    private function hasShippingMethodWithFee(ShippingMethodInterface $shippingMethod, int $fee): bool
    {
        foreach ($this->getCartShippingMethods($this->getCart()) as $cartShippingMethod) {
            if (
                $cartShippingMethod['shippingMethod']['code'] === $shippingMethod->getCode() &&
                $cartShippingMethod['cost'] === $fee
            ) {
                return true;
            }
        }

        return false;
    }

    private function getPossiblePaymentMethods(string $paymentMethodName): array
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($this->sharedStorage->get('cart_token'));

        if (!$order->getLastPayment()) {
            return [];
        }

        $this->client->request(
            Request::METHOD_GET,
            \sprintf('/new-api/shop/orders/%s/payments/%s/methods',
                $this->sharedStorage->get('cart_token'),
                $order->getLastPayment()->getId()),
            [],
            [],
            $this->getHeaders()
        );

        /** @var Response $response */
        $response = $this->client->getResponse();

        return json_decode($response->getContent(), true)['hydra:member'];
    }

    private function hasProductWithNameAndQuantityInCart(string $productName, int $quantity): bool
    {
        /** @var array $items */
        $items = $this->responseChecker->getValue($this->client->getResponse(), 'items');

        foreach ($items as $item) {
            if ($item['variant']['translations']['en_US']['name'] === $productName && $item['quantity'] === $quantity) {
                return true;
            }
        }

        return false;
    }

    private function fillAddress(string $addressType, AddressInterface $address): void
    {
        $this->content[$addressType]['city'] = $address->getCity() ?? '';
        $this->content[$addressType]['street'] = $address->getStreet() ?? '';
        $this->content[$addressType]['postcode'] = $address->getPostcode() ?? '';
        $this->content[$addressType]['countryCode'] = $address->getCountryCode() ?? '';
        $this->content[$addressType]['firstName'] = $address->getFirstName() ?? '';
        $this->content[$addressType]['lastName'] = $address->getLastName() ?? '';
    }

    private function getViolation(array $violations, string $element): array
    {
        return $violations[array_search($element, array_column($violations, 'propertyPath'))];
    }
}
