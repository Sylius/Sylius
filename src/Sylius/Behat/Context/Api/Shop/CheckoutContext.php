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
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    public const CHECKOUT_STATE_TYPES = [
        'address' => OrderCheckoutStates::STATE_ADDRESSED,
        'shipping method' => OrderCheckoutStates::STATE_SHIPPING_SELECTED,
        'payment' => OrderCheckoutStates::STATE_PAYMENT_SELECTED,
    ];

    /** @var ApiClientInterface */
    private $ordersClient;

    /** @var ApiClientInterface */
    private $addressesClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var RepositoryInterface */
    private $shippingMethodRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $paymentMethodRepository;

    /** @var ProductVariantResolverInterface */
    private $productVariantResolver;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string[] */
    private $content = [];

    /** @var string */
    private $paymentMethodClass;

    /** @var string */
    private $shippingMethodClass;

    public function __construct(
        ApiClientInterface $ordersClient,
        ApiClientInterface $addressesClient,
        ResponseCheckerInterface $responseChecker,
        RepositoryInterface $shippingMethodRepository,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $paymentMethodRepository,
        ProductVariantResolverInterface $productVariantResolver,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage,
        string $paymentMethodClass,
        string $shippingMethodClass
    ) {
        $this->ordersClient = $ordersClient;
        $this->addressesClient = $addressesClient;
        $this->responseChecker = $responseChecker;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->productVariantResolver = $productVariantResolver;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodClass = $paymentMethodClass;
        $this->shippingMethodClass = $shippingMethodClass;
    }

    /**
     * @Given /^(my) billing address is fulfilled automatically through default address$/
     */
    public function myBillingAddressIsFulfilledAutomaticallyThroughDefaultAddress(ShopUserInterface $user): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $user->getCustomer();
        Assert::notNull($customer);

        $defaultAddress = $customer->getDefaultAddress();
        Assert::notNull($defaultAddress);

        $this->iSpecifyTheBillingAddressAs($defaultAddress);
    }

    /**
     * @When I specified the billing address
     */
    public function iSpecifiedTheBillingAddress(): void
    {
        $this->addressOrder($this->getArrayWithDefaultAddress());
    }

    /**
     * @Given I have proceeded order with :shippingMethod shipping method and :paymentMethod payment
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
     * @Then I should not be able to address an order with an empty cart
     */
    public function iAmAtTheCheckoutAddressingStep(): void
    {
        // Intentionally left blank
    }

    /**
     * @When /^I choose "([^"]+)" street for (billing|shipping) address$/
     */
    public function iChooseForBillingAddress(string $street, string $addressType): void
    {
        $addressBook = $this->responseChecker->getCollection($this->addressesClient->index());

        $addressType .= 'Address';

        $address = $this->getAddressByFieldValue($addressBook, 'street', $street);

        if ($addressType === 'shippingAddress') {
            $this->content['billingAddress'] = $address;
        }

        $this->content[$addressType] = $address;

        $this->addressOrder($this->content);
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
     * @When /^the visitor changes the billing (address to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^the (?:customer|visitor) specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I specify the billing (address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     * @Given /^the (?:visitor|customer) has specified (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address): void
    {
        $this->fillAddress('billingAddress', $address);
    }

    /**
     * @When /^the visitor try to specify the incorrect billing address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     */
    public function iTryToSpecifyTheIncorrectBillingAddressAs(
        string $city,
        string $street,
        string $postcode,
        string $countryName,
        string $customerName
    ): void {
        $addressType = 'billingAddress';

        $this->addAddress($addressType, $city, $street, $postcode, $customerName, $countryName);
    }

    /**
     * @When /^the visitor try to specify the billing address without country as "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     */
    public function iTryToSpecifyTheBillingAddressWithoutCountryAs(
        string $city,
        string $street,
        string $postcode,
        string $customerName
    ): void {
        $this->addAddress('billingAddress', $city, $street, $postcode, $customerName);
    }

    /**
     * @When /^I specify the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I specify the shipping (address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
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
     * @When /^I try to change the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifiedTheBillingAddressAs(AddressInterface $address): void
    {
        $this->iSpecifyTheEmailAs(null);
        $this->iSpecifyTheBillingAddressAs($address);
        $this->iCompleteTheAddressingStep();
    }

    /**
     * @When /^I specify (billing|shipping) country (province as "[^"]+")$/
     */
    public function iSpecifyCountryProvinceAs(string $addressType, ProvinceInterface $province): void
    {
        $this->content[$addressType . 'Address']['provinceCode'] = $province->getCode();
    }

    /**
     * @When /^I specify the province name manually as "([^"]+)" for (billing|shipping) address$/
     */
    public function iSpecifyTheProvinceNameManuallyAsForAddress(string $provinceName, string $addressType): void
    {
        $this->content[$addressType . 'Address']['provinceName'] = $provinceName;
    }

    /**
     * @When I specify the first and last name as :fullName for billing address
     */
    public function iSpecifyTheFirstAndLastNameAsForBillingAddress(string $fullName): void
    {
        $names = explode(' ', $fullName);

        $this->content['billingAddress']['firstName'] = $names[0];
        $this->content['billingAddress']['lastName'] = $names[1];
    }

    /**
     * @Given /^I have completed addressing step with email "([^"]+)" and ("[^"]+" based billing address)$/
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
     * @When /^the (?:customer|visitor) completes the addressing step$/
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
     * @When I want to complete checkout
     */
    public function iWantToCompleteCheckout(): void
    {
        $response = $this->completeOrder();

        Assert::notSame($response->getStatusCode(), 200);

        $this->ordersClient->show($this->sharedStorage->get('cart_token'));
    }

    /**
     * @Given I confirmed my order
     * @When I confirm my order
     * @When I try to confirm my order
     * @When /^the (?:visitor|customer) confirm his order$/
     */
    public function iConfirmMyOrder(): void
    {
        $response = $this->completeOrder();

        if ($response->getStatusCode() === 422) {
            return;
        }

        $this->sharedStorage->set('response', $response);
        $this->sharedStorage->set(
            'order_number',
            $this->responseChecker->getValue($response, 'number')
        );
    }

    /**
     * @Given I completed the shipping step with :shippingMethod shipping method
     * @When I proceed with :shippingMethod shipping method
     * @When I select :shippingMethod shipping method
     * @When /^the (?:visitor|customer) proceed with ("[^"]+" shipping method)$/
     * @Given /^the (?:visitor|customer) has proceeded ("[^"]+" shipping method)$/
     * @When /^the visitor try to proceed with ("[^"]+" shipping method) in the customer cart$/
     * @When I try to change shipping method to :shippingMethod
     * @Given I proceed selecting :shippingMethod shipping method
     */
    public function iProceededWithShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->selectShippingMethod($shippingMethod);
    }

    /**
     * @When I try to select :shippingMethodCode shipping method
     */
    public function iTryToSelectShippingMethod(string $shippingMethodCode): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            sprintf('shipments/%s', $this->getCart()['shipments'][0]['id'])
        );

        $request->setContent(['shippingMethod' => $this->iriConverter->getItemIriFromResourceClass($this->shippingMethodClass, ['code' => $shippingMethodCode])]);

        $this->ordersClient->executeCustomRequest($request);
    }

    /**
     * @When I try to select :paymentMethodCode payment method
     */
    public function iTryToSelectPaymentMethod(string $paymentMethodCode): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            sprintf('payments/%s', $this->getCart()['payments'][0]['id'])
        );

        $request->setContent(['paymentMethod' => $this->iriConverter->getItemIriFromResourceClass($this->paymentMethodClass, ['code' => $paymentMethodCode])]);

        $this->ordersClient->executeCustomRequest($request);
    }

    /**
     * @Then I should be notified that the order should be addressed first
     */
    public function iShouldBeNotifiedThatTheOrderShouldBeAddressedFirst(): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            'Order should be addressed first.'
        ));
    }

    /**
     * @Then I should be informed that shipping method with code :code does not exist
     */
    public function iShouldBeInformedThatShippingMethodWithCodeDoesNotExist(string $code): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            sprintf('The shipping method with %s code does not exist.', $code)
        ));
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
     * @Given I completed the payment step with :paymentMethod payment method
     * @When I choose :paymentMethod payment method
     * @When I select :paymentMethod payment method
     * @When I have proceeded selecting :paymentMethod payment method
     * @When I proceed selecting :paymentMethod payment method
     * @When /^the (?:customer|visitor) proceed with ("[^"]+" payment)$/
     * @Given /^the (?:customer|visitor) has proceeded ("[^"]+" payment)$/
     * @When I try to change payment method to :paymentMethod payment
     * @When I change payment method to :paymentMethod after checkout
     */
    public function iChoosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            \sprintf('payments/%s', $this->getCart()['payments'][0]['id'])
        );

        $request->setContent(['paymentMethod' => $this->iriConverter->getIriFromItem($paymentMethod)]);

        $this->ordersClient->executeCustomRequest($request);
    }

    /**
     * @When I proceed through checkout process
     */
    public function iProceedThroughCheckoutProcess(): void
    {
        $this->addressOrder($this->getArrayWithDefaultAddress());

        $this->iCompleteTheShippingStepWithFirstShippingMethod();

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([]);
        $this->iChoosePaymentMethod($paymentMethod);
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+") should be filled as (billing) address$/
     * @Then /^the visitor should has ("[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+" specified as) (billing) address$/
     */
    public function addressShouldBeFilledAsBillingAddress(AddressInterface $address, string $addressType): void
    {
        $this->addressShouldBeFilledAs($address, $addressType);
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+") should be filled as (shipping) address$/
     * @Then /^the visitor should has ("[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+" specified as) (shipping) address$/
     */
    public function addressShouldBeFilledAsShippingAddress(AddressInterface $address, string $addressType): void
    {
        $this->addressShouldBeFilledAs($address, $addressType);
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
     * @Then I should not be able to confirm order because products do not fit :shippingMethod requirements
     */
    public function iShouldNotBeAbleToConfirmOrderBecauseDoNotBelongsToShippingCategory(
        ShippingMethodInterface $shippingMethod
    ): void {
        $this->iConfirmMyOrder();

        $response = $this->ordersClient->getLastResponse();

        Assert::same($response->getStatusCode(), 422);

        Assert::true($this->isViolationWithMessageInResponse(
            $response,
            sprintf(
                'Product does not fit requirements for %s shipping method. Please reselect your shipping method.',
                $shippingMethod->getName()
            )
        ));
    }

    /**
     * @Then I should not be able to select :paymentMethod payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->iChoosePaymentMethod($paymentMethod);

        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->ordersClient->getLastResponse(),
                sprintf(
                    'The payment method %s is not available for this order. Please choose another one.',
                    $paymentMethod->getName()
                )
            )
        );
    }

    /**
     * @Then I should be informed that payment method with code :code does not exist
     */
    public function iShouldBeInformedThatPaymentMethodWithCodeDoesNotExist(string $code): void
    {
        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->ordersClient->getLastResponse(),
                sprintf('The payment method with %s code does not exist.', $code)
            )
        );
    }

    /**
     * @Then I should be able to select :paymentMethodName payment method
     */
    public function iShouldBeAbleToSelectPaymentMethod(string $paymentMethodName): void
    {
        $paymentMethods = $this->getPossiblePaymentMethods();

        Assert::notFalse(array_search($paymentMethodName, array_column($paymentMethods, 'name'), true));
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the :choice choice
     */
    public function iShouldHavePaymentMethodAvailableAsTheChoice(string $paymentMethodName, string $choice): void
    {
        $paymentMethods = $this->getPossiblePaymentMethods();
        Assert::notEmpty($paymentMethods);

        if ($choice === 'first') {
            Assert::same(reset($paymentMethods)['name'], $paymentMethodName);
        }
        if ($choice === 'last') {
            Assert::same(end($paymentMethods)['name'], $paymentMethodName);
        }
    }

    /**
     * @Then I should still be on the checkout addressing step
     */
    public function iShouldStillBeOnTheCheckoutAddressingStep(): void
    {
        Assert::same($this->getCart()['checkoutState'], OrderCheckoutStates::STATE_CART);
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
        $response = $this->ordersClient->getLastResponse();

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
        $paymentMethods = $this->getPossiblePaymentMethods();
        $paymentMethodName = $paymentMethod->getName();

        foreach ($paymentMethods as $method) {
            if ($method['name'] === $paymentMethodName) {
                return;
            }
        }

        throw new \InvalidArgumentException('Couldn\'t find given payment method for this order');
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrdersShippingMethodShouldBe(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethods = $this->getCartShippingMethods($this->getCart());
        $shippingMethodName = $shippingMethod->getName();

        foreach ($shippingMethods as $method) {
            if ($method['translations']['en_US']['name'] === $shippingMethodName) {
                return;
            }
        }

        throw new \InvalidArgumentException('Couldn\'t find given shipping method for this order');
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep(): void
    {
        Assert::same($this->getCheckoutState(), OrderCheckoutStates::STATE_ADDRESSED);
    }

    /**
     * @Then I should not be able to proceed checkout shipping step
     */
    public function iShouldNotBeAbleToProceedCheckoutShippingStep(): void
    {
        $this->iShouldBeOnTheCheckoutShippingStep();
        Assert::isEmpty($this->getCart()['shipments']);
    }

    /**
     * @Then I should not be able to proceed checkout payment step
     */
    public function iShouldNotBeAbleToProceedCheckoutPaymentStep(): void
    {
        $this->iShouldBeOnTheCheckoutPaymentStep();
        Assert::isEmpty($this->getCart()['payments']);
    }

    /**
     * @Then I should not be able to proceed checkout complete step
     */
    public function iShouldNotBeAbleToProceedCheckoutCompleteStep(): void
    {
        $this->iShouldBeOnTheCheckoutCompleteStep();
        $this->iConfirmMyOrder();

        $response = $this->ordersClient->getLastResponse();
        Assert::same($this->responseChecker->getError($response), 'An empty order cannot be completed.');
    }

    /**
     * @Then /^the (?:visitor|customer) should have checkout (address|shipping method|payment) step completed$/
     */
    public function theVisitorShouldHaveCheckoutAddressStepCompleted(string $stepType): void
    {
        Assert::same($this->getCheckoutState(), $this::CHECKOUT_STATE_TYPES[$stepType]);
    }

    /**
     * @Then I should be notified that :countryName country does not exist
     */
    public function iShouldBeNotifiedThatCountryDoesNotExist(string $countryName): void
    {
        $this->responseChecker->hasViolationWithMessage(
            $this->ordersClient->getLastResponse(),
            sprintf('The country %s does not exist.', StringInflector::nameToLowercaseCode($countryName))
        );
    }

    /**
     * @Then I should be notified that address without country cannot exist
     */
    public function iShouldBeNotifiedThatAddressWithoutCountryCannotExist(): void
    {
        $this->responseChecker->hasViolationWithMessage(
            $this->ordersClient->getLastResponse(),
            'The address without country cannot exist'
        );
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
     */
    public function iShouldNotSeeShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        Assert::false($this->hasShippingMethod($shippingMethod));
    }

    /**
     * @Then I should not be able to select :shippingMethod shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $response = $this->selectShippingMethod($shippingMethod);

        Assert::same($response->getStatusCode(), 422);
        Assert::true($this->isViolationWithMessageInResponse($response, sprintf(
            'The shipping method %s is not available for this order. Please reselect your shipping method.',
            $shippingMethod->getName()
        )));
    }

    /**
     * @Then I should have :shippingMethod shipping method available as the first choice
     */
    public function iShouldHaveShippingMethodAvailableAsFirstChoice(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethods = $this->getCartShippingMethods($this->getCart());

        Assert::true($shippingMethods[0]['code'] === $shippingMethod->getCode());
    }

    /**
     * @Then I should have :shippingMethod shipping method available as the last choice
     */
    public function iShouldHaveShippingMethodAvailableAsLastChoice(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethods = $this->getCartShippingMethods($this->getCart());

        Assert::true(end($shippingMethods)['code'] === $shippingMethod->getCode());
    }

    /**
     * @Then /^my order total should be ("[^"]+")$/
     */
    public function myOrderTotalShouldBe(int $total): void
    {
        Assert::same($total, (int) $this->getCart()['total']);
    }

    /**
     * @Then /^my order promotion total should be ("[^"]+")$/
     */
    public function myOrderPromotionTotalShouldBe(int $promotionTotal): void
    {
        $responsePromotionTotal = $this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'orderPromotionTotal');

        Assert::same($promotionTotal, $responsePromotionTotal);
    }

    /**
     * @Then /^my tax total should be ("[^"]+")$/
     */
    public function myTaxTotalShouldBe(int $taxTotal): void
    {
        $responseTaxTotal = $this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'taxTotal');

        Assert::same($taxTotal, $responseTaxTotal);
    }

    /**
     * @Then I should have :quantity :productName products in the cart
     */
    public function iShouldHaveProductsInTheCart(int $quantity, string $productName): void
    {
        Assert::true(
            $this->hasProductWithNameAndQuantityInCart($productName, $quantity),
            sprintf('There is no product %s with quantity %d.', $productName, $quantity)
        );
    }

    /**
     * @Then /^my discount should be ("[^"]+")$/
     * @Then there should be no discount
     */
    public function myDiscountShouldBe(int $discount = 0): void
    {
        if ($this->sharedStorage->has('cart_token')) {
            $discountTotal = $this->responseChecker->getValue(
                $this->ordersClient->show($this->sharedStorage->get('cart_token')),
                'orderPromotionTotal'
            );

            Assert::same($discount, (int) $discountTotal);

            return;
        }

        Assert::same($this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'orderPromotionTotal'), $discount);
    }

    /**
     * @Then there should be no taxes charged
     */
    public function thereShouldBeNoTaxesCharged(): void
    {
        $this->ordersClient->show($this->sharedStorage->get('cart_token'));

        Assert::same($this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'taxTotal'), 0);
    }

    /**
     * @Then my order's locale should be :localeCode
     */
    public function myOrderLocaleShouldBe(string $localeCode): void
    {
        Assert::same($this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'localeCode'), $localeCode);
    }

    /**
     * @Then /^my order shipping should be ("(?:\£|\$)\d+(?:\.\d+)?")$/
     */
    public function myOrderShippingShouldBe(int $price): void
    {
        Assert::same($this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'shippingTotal'), $price);
    }

    /**
     * @Then I should not see shipping total
     */
    public function iShouldNotSeeShippingTotal(): void
    {
        Assert::same($this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'shippingTotal'), 0);
    }

    /**
     * @Then /^I should(?:| also) be notified that the "([^"]+)" and the "([^"]+)" in (shipping|billing) details are required$/
     */
    public function iShouldBeNotifiedThatTheAndTheInShippingDetailsAreRequired(string $firstElement, string $secondElement, string $detailType): void
    {
        $response = $this->ordersClient->getLastResponse();

        Assert::true($response->getStatusCode() === 422);

        /** @var array|null $violations */
        $violations = $this->responseChecker->getResponseContent($response)['violations'];

        $detailType .= 'Address';

        foreach ([$firstElement, $secondElement] as $element) {
            $violation = $this->getViolation(
                $violations,
                $detailType . '.' . StringInflector::nameToCamelCase($element)
            );
            Assert::same($violation['message'], sprintf('Please enter %s.', $element));
        }
    }

    /**
     * @Then /^I should be informed that (this product) has been disabled$/
     * @Then /^I should be informed that (product "[^"]+") is disabled$/
     */
    public function iShouldBeInformedThatThisProductHasBeenDisabled(ProductInterface $product): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            sprintf('This product %s has been disabled.', $product->getName())
        ));
    }

    /**
     * @Then /^I should be informed that (product "[^"]+") does not exist$/
     */
    public function iShouldBeInformedThatThisProductDoesNotExist(ProductInterface $product): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            sprintf('The product %s does not exist.', $product->getName())
        ));
    }

    /**
     * @Then /^I should be informed that ("([^"]*)" product variant) does not exist$/
     */
    public function iShouldBeInformedThatProductVariantDoesNotExist(ProductVariantInterface $productVariant): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            sprintf('The product variant with %s does not exist.', $productVariant->getCode())
        ));
    }

    /**
     * @Then I should be informed that product variant with code :code does not exist
     */
    public function iShouldBeInformedThatProductVariantWithCodeDoesNotExist(string $code): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->ordersClient->getLastResponse(),
            sprintf('The product variant with %s does not exist.', $code)
        ));
    }

    /**
     * @Then I should not see the thank you page
     */
    public function iShouldNotSeeTheThankYouPage(): void
    {
        Assert::same($this->ordersClient->getLastResponse()->getStatusCode(), 422);
    }

    /**
     * @Then address to :fullName should be used for both :addressType1 and :addressType2 of my order
     * @Then my order's :addressType address should be to :fullName
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress(string $fullName, string ...$addressTypes): void
    {
        foreach ($addressTypes as $addressType) {
            $this->hasFullNameInAddress($fullName, $addressType);
        }
    }

    /**
     * @Then I should see :provinceName in the :addressType address
     */
    public function iShouldSeeInTheBillingAddress(string $provinceName, string $addressType): void
    {
        $this->hasProvinceNameInAddress($provinceName, $addressType);
    }

    /**
     * @Then /^I should be informed that (this payment method) has been disabled$/
     */
    public function iShouldBeInformedThatThisPaymentMethodHasBeenDisabled(PaymentMethodInterface $paymentMethod): void
    {
        $response = $this->ordersClient->getLastResponse();

        Assert::same($response->getStatusCode(), 422);

        Assert::true($this->isViolationWithMessageInResponse(
            $response,
            sprintf(
                'This payment method %s has been disabled. Please reselect your payment method.',
                $paymentMethod->getName()
            )
        ));
    }

    /**
     * @When /^I try to add (product "[^"]+") to the (cart)$/
     */
    public function iTryToAddProductToCart(ProductInterface $product, string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue);
    }

    /**
     * @When /^I try to add ("([^"]+)" product variant)$/
     * @When /^I try to add ("([^"]+)" variant of product "([^"]+)")$/
     */
    public function iTryToAddProductVariant(ProductVariantInterface $productVariant): void
    {
        $tokenValue = $this->getCartTokenValue();
        $this->putVariantToCart($productVariant, $tokenValue);
    }

    /**
     * @When /^I try to add (product "[^"]+") with variant code "([^"]+)"$/
     */
    public function iTryToAddProductVariantWithCode(ProductInterface $product, string $code): void
    {
        $tokenValue = $this->getCartTokenValue();

        $request = Request::customItemAction(
            'shop',
            'orders',
            $tokenValue,
            HTTPRequest::METHOD_PATCH,
            'items'
        );

        $request->setContent([
            'productVariant' => $this->iriConverter->getItemIriFromResourceClass(\get_class($product->getVariants()->first()), ['code' => $code]),
            'quantity' => 1,
        ]);

        $this->sharedStorage->set('response', $this->ordersClient->executeCustomRequest($request));
    }

    /**
     * @When /^I try to remove (product "[^"]+") from the (cart)$/
     */
    public function iTryToRemoveProductFromTheCart(ProductInterface $product, string $tokenValue): void
    {
        $this->removeOrderItemFromCart($product->getId(), $tokenValue);
    }

    /**
     * @When /^I try to change quantity to (\d+) of (product "[^"]+") from the (cart)$/
     */
    public function iTryToChangeQuantityToOfProductFromTheCart(int $quantity, ProductInterface $product, string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue, $quantity);
    }

    /**
     * @Then I should be informed that cart is no longer available
     */
    public function iShouldBeInformedThatCartIsNoLongerAvailable(): void
    {
        $response = $this->ordersClient->getLastResponse();

        Assert::same($response->getStatusCode(), 404);

        Assert::same($this->responseChecker->getResponseContent($response)['message'], 'Not Found');
    }

    /**
     * @Then I should not be able to specify province name manually for shipping address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForShippingAddress(): void
    {
        $this->iCompleteTheAddressingStep();

        $this->assertProvinceMessage('shippingAddress');
    }

    /**
     * @Then I should not be able to specify province name manually for billing address
     * @Then I should be notified that selected province is invalid for billing address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForBillingAddress(): void
    {
        $this->assertProvinceMessage('billingAddress');
    }

    /**
     * @Then I should be notified that product :product does not have sufficient stock
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantResolver->getVariant($product);

        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->ordersClient->getLastResponse(),
            sprintf('The product variant with %s name does not have sufficient stock.', $variant->getName())
        ));
    }

    private function assertProvinceMessage(string $addressType): void
    {
        $response = $this->ordersClient->getLastResponse();

        Assert::same($response->getStatusCode(), 422);
        Assert::true($this->isViolationWithMessageInResponse(
            $response,
            'Please select proper province.',
            $addressType
        ));
    }

    private function isViolationWithMessageInResponse(Response $response, string $message, ?string $property = null): bool
    {
        $violations = $this->responseChecker->getResponseContent($response)['violations'];
        foreach ($violations as $violation) {
            if ($violation['message'] === $message && $property === null) {
                return true;
            }

            if ($violation['message'] === $message && $property !== null && $violation['propertyPath'] === $property) {
                return true;
            }
        }

        return false;
    }

    private function addressOrder(array $content): void
    {
        if (!array_key_exists('email', $content)) {
            $content['email'] = null;
        }

        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->getCartTokenValue(),
            HTTPRequest::METHOD_PATCH,
            'address'
        );

        $request->setContent($content);

        $this->ordersClient->executeCustomRequest($request);
    }

    private function getCart(): array
    {
        return $this->responseChecker->getResponseContent($this->ordersClient->show($this->getCartTokenValue()));
    }

    private function getCartTokenValue(): ?string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        if ($this->sharedStorage->has('previous_cart_token')) {
            return $this->sharedStorage->get('previous_cart_token');
        }

        return null;
    }

    private function getCheckoutState(): string
    {
        $this->ordersClient->show($this->sharedStorage->get('cart_token'));

        $response = $this->ordersClient->getLastResponse();

        return $this->responseChecker->getValue($response, 'checkoutState');
    }

    private function getCartShippingMethods(array $cart): array
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $cart['tokenValue'],
            HTTPRequest::METHOD_GET,
            sprintf('shipments/%s/methods', $cart['shipments'][0]['id'])
        );

        $this->ordersClient->executeCustomRequest($request);

        return $this->responseChecker->getCollection($this->ordersClient->getLastResponse());
    }

    private function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool
    {
        foreach ($this->getCartShippingMethods($this->getCart()) as $cartShippingMethod) {
            if ($cartShippingMethod['code'] === $shippingMethod->getCode()) {
                return true;
            }
        }

        return false;
    }

    private function hasShippingMethodWithFee(ShippingMethodInterface $shippingMethod, int $fee): bool
    {
        foreach ($this->getCartShippingMethods($this->getCart()) as $cartShippingMethod) {
            if (
                $cartShippingMethod['price'] === $fee &&
                $cartShippingMethod['code'] === $shippingMethod->getCode()
            ) {
                return true;
            }
        }

        return false;
    }

    private function getPossiblePaymentMethods(): array
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($this->sharedStorage->get('cart_token'));
        Assert::notNull($order);

        if (!$order->getLastPayment()) {
            return [];
        }

        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_GET,
            sprintf('payments/%s/methods', $order->getLastPayment()->getId())
        );

        $this->ordersClient->executeCustomRequest($request);

        return $this->responseChecker->getCollection($this->ordersClient->getLastResponse());
    }

    private function hasProductWithNameAndQuantityInCart(string $productName, int $quantity): bool
    {
        /** @var array $items */
        $items = $this->responseChecker->getValue($this->ordersClient->getLastResponse(), 'items');

        foreach ($items as $item) {
            if ($item['productName'] === $productName && $item['quantity'] === $quantity) {
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
        $this->content[$addressType]['provinceName'] = $address->getProvinceName();
    }

    private function getArrayWithDefaultAddress(): array
    {
        return [
            'email' => 'rich@sylius.com',
            'billingAddress' => [
                'city' => 'New York',
                'street' => 'Wall Street',
                'postcode' => '00-001',
                'countryCode' => 'US',
                'firstName' => 'Richy',
                'lastName' => 'Rich',
            ],
        ];
    }

    private function getViolation(array $violations, string $element): array
    {
        return $violations[array_search($element, array_column($violations, 'propertyPath'), true)];
    }

    private function hasFullNameInAddress(string $fullName, string $addressType): void
    {
        $response = $this->ordersClient->getLastResponse();

        $names = explode(' ', $fullName);
        $addressType .= 'Address';

        Assert::same(
            $this->responseChecker->getResponseContent($response)[$addressType]['firstName'],
            $names[0]
        );
        Assert::same(
            $this->responseChecker->getResponseContent($response)[$addressType]['lastName'],
            $names[1]
        );
    }

    private function hasProvinceNameInAddress(string $provinceName, string $addressType): void
    {
        $response = $this->ordersClient->getLastResponse();
        $addressType .= 'Address';

        Assert::same(
            $this->responseChecker->getResponseContent($response)[$addressType]['provinceName'],
            $provinceName
        );
    }

    private function putProductToCart(ProductInterface $product, string $tokenValue, int $quantity = 1): void
    {
        Assert::notNull($productVariant = $this->productVariantResolver->getVariant($product));

        $this->putVariantToCart($productVariant, $tokenValue, $quantity);
    }

    private function putVariantToCart(ProductVariantInterface $productVariant, string $tokenValue, int $quantity = 1): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $tokenValue,
            HTTPRequest::METHOD_PATCH,
            'items'
        );

        $request->setContent([
            'productVariant' => $this->iriConverter->getIriFromItem($productVariant),
            'quantity' => $quantity,
        ]);

        $this->sharedStorage->set('response', $this->ordersClient->executeCustomRequest($request));
    }

    private function removeOrderItemFromCart(int $orderItemId, string $tokenValue): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $tokenValue,
            HttpRequest::METHOD_DELETE,
            \sprintf('items/%s', $orderItemId)
        );

        $this->sharedStorage->set('response', $this->ordersClient->executeCustomRequest($request));
    }

    private function getAddressByFieldValue(array $addressBook, string $fieldName, string $fieldValue): array
    {
        foreach ($addressBook as $address) {
            if ($address[$fieldName] === $fieldValue) {
                return $address;
            }
        }

        return [];
    }

    private function addressesAreEqual(array $address, AddressInterface $addressToCompare): bool
    {
        if (
            $address['firstName'] === $addressToCompare->getFirstName() &&
            $address['lastName'] === $addressToCompare->getLastName() &&
            $address['countryCode'] === $addressToCompare->getCountryCode() &&
            $address['street'] === $addressToCompare->getStreet() &&
            $address['city'] === $addressToCompare->getCity() &&
            $address['postcode'] === $addressToCompare->getPostcode() &&
            ($addressToCompare->getProvinceName() !== null && isset($address['provinceName'])) ?
                $address['provinceName'] === $addressToCompare->getProvinceName() : true
        ) {
            return true;
        }

        return false;
    }

    private function addressShouldBeFilledAs(AddressInterface $address, string $addressType): void
    {
        $response = $this->ordersClient->show($this->sharedStorage->get('cart_token'));

        $addressFromResponse = $this->responseChecker->getValue($response, $addressType . 'Address');

        Assert::true($this->addressesAreEqual($addressFromResponse, $address));
    }

    private function completeOrder(): Response
    {
        $notes = $this->content['additionalNote'] ?? null;

        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            'complete'
        );

        $request->setContent(['notes' => $notes]);

        return $this->ordersClient->executeCustomRequest($request);
    }

    private function selectShippingMethod(ShippingMethodInterface $shippingMethod): Response
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            sprintf('shipments/%s', $this->getCart()['shipments'][0]['id'])
        );

        $request->setContent(['shippingMethod' => $this->iriConverter->getIriFromItem($shippingMethod)]);

        return $this->ordersClient->executeCustomRequest($request);
    }

    private function addAddress(
        string $addressType,
        string $city,
        string $street,
        string $postcode,
        string $customerName,
        ?string $countryName = null
    ): void {
        [$firstName, $lastName] = explode(' ', $customerName);

        $this->content[$addressType]['city'] = $city;
        $this->content[$addressType]['street'] = $street;
        $this->content[$addressType]['postcode'] = $postcode;
        $this->content[$addressType]['firstName'] = $firstName;
        $this->content[$addressType]['lastName'] = $lastName;
        $this->content[$addressType]['countryCode'] = $countryName !== null ? StringInflector::nameToLowercaseCode($countryName) : null;
    }
}
