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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Context\Api\Subresources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SecurityServiceInterface $adminSecurityService,
        private SharedStorageInterface $sharedStorage,
        private SharedSecurityServiceInterface $sharedSecurityService,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
    ) {
    }

    /**
     * @Given /^I am viewing the summary of (this order)$/
     * @Given I am viewing the summary of the order :order
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $response = $this->client->show(Resources::ORDERS, $order->getTokenValue());
        Assert::same($this->responseChecker->getValue($response, '@id'), $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'));

        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Given I am browsing orders
     * @When I browse orders
     */
    public function iBrowseOrders(): void
    {
        $this->client->index(Resources::ORDERS);
    }

    /**
     * @When I browse order's :order history
     */
    public function iBrowseOrderHistory(OrderInterface $order): void
    {
        $this->iSeeTheOrder($order);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->addFilter('channel.code', $channel->getCode());
    }

    /**
     * @When I specify filter date from as :dateTime
     */
    public function iSpecifyFilterDateFromAs(string $dateTime): void
    {
        $this->client->addFilter('checkoutCompletedAt[after]', $dateTime);
    }

    /**
     * @When specify its tracking code as :trackingCode
     */
    public function specifyItsTrackingCodeAs(string $trackingCode): void
    {
        $shipment = $this->sharedStorage->get('order')->getShipments()->first();

        $this->client->buildUpdateRequest(
            Resources::SHIPMENTS,
            (string) $shipment->getId(),
        );

        $this->client->addRequestData('tracking', $trackingCode);
        $this->client->update();
    }

    /**
     * @When /^I try to view the summary of the (customer's latest cart)$/
     */
    public function iTryToViewTheSummaryOfTheCustomersLatestCart(OrderInterface $cart): void
    {
        $this->client->show(Resources::ORDERS, $cart->getTokenValue());
    }

    /**
     * @When I specify filter date to as :dateTime
     */
    public function iSpecifyFilterDateToAs(string $dateTime): void
    {
        $this->client->addFilter('checkoutCompletedAt[before]', $dateTime);
    }

    /**
     * @When I resend the order confirmation email
     */
    public function iResendTheOrderConfirmationEmail(): void
    {
        $this->client->customItemAction(
            Resources::ORDERS,
            $this->sharedStorage->get('order')->getTokenValue(),
            HttpRequest::METHOD_POST,
            'resend-confirmation-email',
        );
    }

    /**
     * @When I filter by product :productName
     * @When I filter by products :firstProduct and :secondProduct
     */
    public function iFilterByProduct(string ...$productNames): void
    {
        foreach ($productNames as $productName) {
            $this->client->addFilter('items.productName[]', $productName);
        }

        $this->client->filter();
    }

    /**
     * @When I resend the shipment confirmation email
     */
    public function iResendTheShipmentConfirmationEmail(): void
    {
        $this->client->customItemAction(
            Resources::SHIPMENTS,
            (string) $this->sharedStorage->get('order')->getShipments()->last()->getId(),
            HttpRequest::METHOD_POST,
            'resend-confirmation-email',
        );
    }

    /**
     * @When I choose :shippingMethod as a shipping method filter
     */
    public function iChooseAsAShippingMethodFilter(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->addFilter('shipments.method.code', $shippingMethod->getCode());
    }

    /**
     * @When I choose :currency as the filter currency
     */
    public function iChooseCurrencyAsTheFilterCurrency(CurrencyInterface $currency): void
    {
        $this->client->addFilter('currencyCode', $currency->getCode());
    }

    /**
     * @When I specify filter total being greater than :total
     */
    public function iSpecifyFilterTotalBeingGreaterThan(string $total): void
    {
        if (str_contains($total, '.')) {
            $total = str_replace('.', '', $total);
            $this->client->addFilter('total[gt]', $total);

            return;
        }

        $this->client->addFilter('total[gt]', $total . '00');
    }

    /**
     * @When I specify filter total being less than :total
     */
    public function iSpecifyFilterTotalBeingLessThan(string $total): void
    {
        $this->client->addFilter('total[lt]', $total . '00');
    }

    /**
     * @When I filter by variant :variantName
     * @When I filter by variants :firstVariant and :secondVariant
     */
    public function iFilterByVariant(string ...$variantsNames): void
    {
        foreach ($variantsNames as $variantName) {
            $this->client->addFilter('items.variant.translations.name[]', $variantName);
        }

        $this->client->filter();
    }

    /**
     * @When I switch the way orders are sorted by :fieldName
     */
    public function iSwitchSortingBy(string $fieldName): void
    {
        $this->client->addFilter('order[number]', 'asc');
        $this->client->filter();
    }

    /**
     * @When /^I cancel (this order)$/
     */
    public function iCancelThisOrder(OrderInterface $order): void
    {
        $this->client->applyTransition(
            Resources::ORDERS,
            $this->responseChecker->getValue($this->client->show(Resources::ORDERS, $order->getTokenValue()), 'tokenValue'),
            OrderTransitions::TRANSITION_CANCEL,
        );
    }

    /**
     * @When /^I mark (this order) as paid$/
     */
    public function iMarkThisOrderAsAPaid(OrderInterface $order): void
    {
        $this->client->applyTransition(
            Resources::PAYMENTS,
            (string) $order->getLastPayment()->getId(),
            PaymentTransitions::TRANSITION_COMPLETE,
        );
    }

    /**
     * @When /^I ship (this order)$/
     */
    public function iShipThisOrder(OrderInterface $order): void
    {
        $shipment = $order->getShipments()->last();
        Assert::notNull($shipment, 'There is no shipment for this order');

        $this->client->applyTransition(
            Resources::SHIPMENTS,
            (string) $shipment->getId(),
            ShipmentTransitions::TRANSITION_SHIP,
        );

        $this->sharedStorage->set('shipment', $shipment);
    }

    /**
     * @When I limit number of items to :limit
     */
    public function iLimitNumberOfItemsTo(int $limit): void
    {
        $this->client->addFilter('itemsPerPage', $limit);
        $this->client->filter();
    }

    /**
     * @When I check :itemName data
     */
    public function iCheckData(string $itemName): void
    {
        /** @var string $lastResponseContent */
        $lastResponseContent = $this->client->getLastResponse()->getContent();
        /** @var array{productName: string}[] $items */
        $items = json_decode($lastResponseContent, true)['items'];

        foreach ($items as $item) {
            if ($item['productName'] === $itemName) {
                $this->sharedStorage->set('item', $item);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('There is no item with name "%s".', $itemName));
    }

    /**
     * @Then I should see a single order from customer :customer
     */
    public function iShouldSeeASingleOrderFromCustomer(CustomerInterface $customer): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'customer',
                $this->iriConverter->getIriFromResource($customer),
            ),
            sprintf('There is no order for customer %s', $customer->getEmail()),
        );
    }

    /**
     * @Then /^I should be notified that the (order|shipment) confirmation email has been successfully resent to the customer$/
     */
    public function iShouldBeNotifiedThatTheOrderConfirmationEmailHasBeenSuccessfullyResentToTheCustomer(): void
    {
        $this->responseChecker->isCreationSuccessful($this->client->getLastResponse());
    }

    /**
     * @Then it should( still) have a :state state
     */
    public function itShouldHaveState(string $state): void
    {
        Assert::true($this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'state', $state));
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), 1);
    }

    /**
     * @Then I should see a single order in the list
     * @Then I should see :number orders in the list
     */
    public function iShouldSeeASingleOrderInTheList(int $number = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $number);
    }

    /**
     * @Then I should be notified that it has been successfully updated
     */
    public function iShouldBeNotifiedAboutItHasBeenSuccessfullyCanceled(): void
    {
        $response = $this->client->getLastResponse();
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($response),
            'Resource could not be completed. Reason: ' . $response->getContent(),
        );
    }

    /**
     * @Then this order should have state :state
     * @Then its state should be :state
     */
    public function itsStateShouldBe(string $state): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $orderState = $this->responseChecker->getValue($this->client->show(Resources::ORDERS, $order->getTokenValue()), 'state');

        Assert::same($orderState, strtolower($state));
    }

    /**
     * @Then /^(it) should have shipment in state "([^"]+)"$/
     * @Then /^(order "[^"]+") should have shipment state "([^"]+)"$/
     */
    public function itShouldHaveShipmentState(OrderInterface $order, string $state): void
    {
        $shipmentIri = $this->responseChecker->getValue(
            $this->client->show(Resources::ORDERS, $order->getTokenValue()),
            'shipments',
        )[0];

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($shipmentIri['@id']), 'state', strtolower($state)),
            sprintf('Shipment for this order is not %s', $state),
        );
    }

    /**
     * @Then it should have payment state :state
     */
    public function itShouldHavePaymentState($state): void
    {
        $paymentIri = $this->responseChecker->getValue(
            $this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue()),
            'payments',
        )[0];

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($paymentIri['@id']), 'state', strtolower($state)),
            sprintf('payment for this order is not %s', $state),
        );
    }

    /**
     * @Then /^there should be(?:| only) (\d+) payments?$/
     */
    public function theOrderShouldHaveNumberOfPayments(int $number): void
    {
        Assert::count(
            $this->responseChecker->getValue($this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue()), 'payments'),
            $number,
        );
    }

    /**
     * @Then the order :order should have order payment state :orderPaymentState
     * @Then /^(this order) should have order payment state "([^"]+)"$/
     */
    public function theOrderShouldHavePaymentState(OrderInterface $order, string $paymentState): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::ORDERS, $order->getTokenValue()), 'paymentState', strtolower($paymentState)),
            sprintf('Order %s does not have %s payment state', $order->getTokenValue(), $paymentState),
        );
    }

    /**
     * @Then it should have :amount items
     */
    public function itShouldHaveAmountOfItems(int $amount): void
    {
        Assert::count($this->responseChecker->getValue($this->client->getLastResponse(), 'items'), $amount);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList(string $productName): void
    {
        $items = $this->responseChecker->getValue($this->client->getLastResponse(), 'items');

        foreach ($items as $item) {
            if ($item['productName'] === $productName) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no product with given name.');
    }

    /**
     * @Then /^the order's shipping total should be ("[^"]+")$/
     */
    public function theOrdersShippingTotalShouldBe(int $shippingTotal): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'shippingTotal'), $shippingTotal);
    }

    /**
     * @Then /^the order's tax total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersTaxTotalShouldBe(int $taxTotal): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'taxTotal'), $taxTotal);
    }

    /**
     * @Then I should not be able to resend the shipment confirmation email
     */
    public function iShouldNotBeAbleToResendTheShipmentConfirmationEmail(): void
    {
        $this->client->customItemAction(
            Resources::SHIPMENTS,
            (string) $this->sharedStorage->get('order')->getShipments()->last()->getId(),
            HttpRequest::METHOD_POST,
            'resend-confirmation-email',
        );

        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot resend shipment confirmation email for shipment in state ready.',
        );
    }

    /**
     * @Then /^the order's items total should be ("[^"]+")$/
     */
    public function theOrdersItemsTotalShouldBe(int $itemsTotal): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'itemsTotal'), $itemsTotal);
    }

    /**
     * @Then /^the order's payment should(?:| also) be ("[^"]+")$/
     */
    public function theOrdersPaymentShouldBe(int $paymentAmount): void
    {
        $response = $this->client->showByIri(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'payments')[0]['@id'],
        );

        Assert::same($this->responseChecker->getValue($response, 'amount'), $paymentAmount);
    }

    /**
     * @Then /^I should not be able to cancel (this order)$/
     */
    public function iShouldNotBeAbleToCancelThisOrder(OrderInterface $order): void
    {
        $this->iCancelThisOrder($order);
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot cancel the order.',
        );
    }

    /**
     * @Then /^the order's total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersTotalShouldBe(int $total): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue());

        Assert::same(
            $this->responseChecker->getValue($response, 'total'),
            $total,
        );
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersPromotionTotalShouldBe(int $promotionTotal): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue());

        Assert::same(
            $this->responseChecker->getValue($response, 'orderPromotionTotal'),
            $promotionTotal,
        );
    }

    /**
     * @Then the order's promotion discount should be :promotionAmount from :promotionName promotion
     */
    public function theOrdersPromotionDiscountShouldBeFromPromotion(string $promotionAmount, string $promotionName): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->getAdjustmentsResponseForOrder(true),
            [
                'type' => AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
                'label' => $promotionName,
                'amount' => $this->getTotalAsInt($promotionAmount),
            ],
        );
    }

    /**
     * @Then the order's shipping promotion should be :promotionAmount
     */
    public function theOrdersShippingPromotionDiscountShouldBe(string $promotionAmount): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->getAdjustmentsResponseForOrder(true),
            [
                'type' => AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
                'amount' => $this->getTotalAsInt($promotionAmount),
            ],
        );
    }

    /**
     * @Then there should be a shipping charge :shippingCharge for :shippingMethodName method
     */
    public function thereShouldBeAShippingChargeForMethod(string $shippingCharge, string $shippingMethodName): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->getAdjustmentsResponseForOrder(true),
            [
                'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
                'label' => $shippingMethodName,
                'amount' => $this->getTotalAsInt($shippingCharge),
            ],
        );
    }

    /**
     * @Then there should be a shipping tax :shippingTax for :shippingMethodName method
     */
    public function thereShouldBeAShippingTaxForMethod(string $shippingTax, string $shippingMethodName): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->getAdjustmentsResponseForOrder(true),
            [
                'type' => AdjustmentInterface::TAX_ADJUSTMENT,
                'label' => $shippingMethodName,
                'amount' => $this->getTotalAsInt($shippingTax),
            ],
        );
    }

    /**
     * @Then /^(the administrator) should see that (order placed by "[^"]+") has "([^"]+)" currency$/
     */
    public function theAdministratorShouldSeeThatThisOrderHasBeenPlacedIn(
        AdminUserInterface $user,
        OrderInterface $order,
        string $currency,
    ): void {
        $this->adminSecurityService->logIn($user);

        $currencyCode = $this->responseChecker->getValue($this->client->show(Resources::ORDERS, $order->getTokenValue()), 'currencyCode');

        Assert::same($currencyCode, $currency);
    }

    /**
     * @Then I should see an order with :orderNumber number
     */
    public function iShouldSeeOrderWithNumber(string $orderNumber): void
    {
        $response = $this->client->getLastResponse();

        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'number', $orderNumber),
            sprintf('No order with number "%s" has been found.', $orderNumber),
        );
    }

    /**
     * @Then I should not see an order with :orderNumber number
     */
    public function iShouldNotSeeOrderWithNumber(string $orderNumber)
    {
        $response = $this->client->getLastResponse();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'number', $orderNumber),
            sprintf('The order with number "%s" has been found, but should not.', $orderNumber),
        );
    }

    /**
     * @Then I should not see any orders with currency :currencyCode
     */
    public function iShouldNotSeeAnyOrderWithCurrency(string $currencyCode): void
    {
        $response = $this->client->getLastResponse();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'currencyCode', $currencyCode),
            sprintf('The order with currency code "%s" has been found, but should not.', $currencyCode),
        );
    }

    /**
     * @Then the first order should have number :number
     */
    public function theFirstOrderShouldHaveNumber(string $number): void
    {
        $items = $this->responseChecker->getValue($this->client->getLastResponse(), 'hydra:member');
        $firstItem = $items[0];

        Assert::same($firstItem['number'], str_replace('#', '', $number));
    }

    /**
     * @Then /^I should see the order "([^"]+)" with total ("[^"]+")$/
     */
    public function iShouldSeeTheOrderWithTotal(string $orderNumber, int $total): void
    {
        $order = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'number',
            trim($orderNumber, '#'),
        )[0];

        Assert::same(
            $order['total'],
            $total,
        );
    }

    /**
     * @Then the administrator should see the order with total :total in order list
     */
    public function theAdministratorShouldSeeTheOrderWithTotalInOrderList(string $total): void
    {
        $adminUser = $this->sharedStorage->get('administrator');
        $currencyCode = $this->getCurrencyCodeFromTotal($total);
        $total = $this->getTotalAsInt($total);

        $this->sharedSecurityService->performActionAsAdminUser(
            $adminUser,
            fn () => $this->client->index(Resources::ORDERS),
        );

        $itemsWithCurrency = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'currencyCode',
            $currencyCode,
        );

        $firstItem = array_pop($itemsWithCurrency);

        Assert::notEmpty($firstItem);
        Assert::same($firstItem['total'], $total);
    }

    /**
     * @Then it should have been placed by the customer :customer
     */
    public function itShouldHaveBeenPlacedByTheCustomer(CustomerInterface $customer): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'customer'),
            $this->iriConverter->getIriFromResource($customer),
        );
    }

    /**
     * @Then it should be shipped via the :shippingMethod shipping method
     */
    public function itShouldBeShippedViaTheShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'shipments')[0]['method'],
            $this->iriConverter->getIriFromResource($shippingMethod),
        );
    }

    /**
     * @Then it should be paid with :paymentMethod
     */
    public function itShouldBePaidWith(PaymentMethodInterface $paymentMethod): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'payments')[0]['method'],
            $this->iriConverter->getIriFromResource($paymentMethod),
        );
    }

    /**
     * @Then it should have no shipping address set
     */
    public function itShouldHaveNoShippingAddressSet(): void
    {
        Assert::false($this->responseChecker->hasKey($this->client->getLastResponse(), 'shippingAddress'));
    }

    /**
     * @Then it should be shipped to :customerName, :street, :postcode, :city, :countryName
     */
    public function itShouldBeShippedTo(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        $shippingAddress = $this->responseChecker->getValue($this->client->getLastResponse(), 'shippingAddress');

        $this->itShouldBeAddressedTo(
            $shippingAddress,
            $customerName,
            $street,
            $postcode,
            $city,
            $countryName,
        );
    }

    /**
     * @Then it should have :customerName, :street, :postcode, :city, :countryName as its billing address
     */
    public function itShouldHaveAddressAsItBillingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        $billingAddress = $this->responseChecker->getValue($this->client->getLastResponse(), 'billingAddress');

        $this->itShouldBeAddressedTo(
            $billingAddress,
            $customerName,
            $street,
            $postcode,
            $city,
            $countryName,
        );
    }

    /**
     * @Then I should see :provinceName as province in the shipping address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress(string $provinceName): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'shippingAddress')['provinceName'],
            $provinceName,
        );
    }

    /**
     * @Then I should see :provinceName as province in the billing address
     */
    public function iShouldSeeAsProvinceInTheBillingAddress(string $provinceName): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'billingAddress')['provinceName'],
            $provinceName,
        );
    }

    /**
     * @Then I should see the shipping date as :dateTime
     */
    public function iShouldSeeTheShippingDateAs(string $dateTime): void
    {
        $response = $this->client->show(Resources::SHIPMENTS, (string) $this->sharedStorage->get('shipment')->getId());

        Assert::same(
            $this->responseChecker->getValue($response, 'shippedAt'),
            (new \DateTime($dateTime))->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @Then /^(its) unit price should be ([^"]+)$/
     */
    public function itemUnitPriceShouldBe(array $orderItem, string $unitPrice): void
    {
        Assert::same($this->getTotalAsInt($unitPrice), $orderItem['unitPrice']);
    }

    /**
     * @Then /^(its) total should be ([^"]+)$/
     */
    public function itemTotalShouldBe(array $orderItem, string $total): void
    {
        Assert::same($this->getTotalAsInt($total), $orderItem['total']);
    }

    /**
     * @Then /^(its) code should be "([^"]+)"$/
     */
    public function itemCodeShouldBe(array $orderItem, string $code): void
    {
        Assert::endsWith($orderItem['variant'], $code);
    }

    /**
     * @Then /^(its) quantity should be ([^"]+)$/
     */
    public function itemQuantityShouldBe(array $orderItem, int $quantity): void
    {
        Assert::same($quantity, $orderItem['quantity']);
    }

    /**
     * @Then /^its discounted unit price should be ([^"]+)$/
     */
    public function itemDiscountedUnitPriceShouldBe(string $discountedUnitPrice): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->getAdjustmentsResponseForOrder(),
            [
                'type' => AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
                'amount' => $this->getTotalAsInt($discountedUnitPrice),
            ],
        );
    }

    /**
     * @Then /^its subtotal should be ([^"]+)$/
     */
    public function itemSubtotalShouldBe(string $subtotal): void
    {
        $orderItem = $this->sharedStorage->get('item');

        $unitPromotionAdjustments = 0;
        foreach ($this->responseChecker->getCollection($this->client->getLastResponse()) as $item) {
            if (in_array($item['type'], [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT])) {
                $unitPromotionAdjustments += $item['amount'];
            }
        }

        Assert::same($this->getTotalAsInt($subtotal), $orderItem['unitPrice'] * $orderItem['quantity'] + $unitPromotionAdjustments);
    }

    /**
     * @Then /^its discount should be ([^"]+)$/
     */
    public function theItemShouldHaveDiscount(string $discount): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            [
                'type' => AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
                'amount' => $this->getTotalAsInt($discount),
            ],
        );
    }

    /**
     * @Then /^its tax should be ([^"]+)$/
     */
    public function itemTaxShouldBe(string $tax): void
    {
        $this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            [
                'type' => AdjustmentInterface::TAX_ADJUSTMENT,
                'amount' => $this->getTotalAsInt($tax),
            ],
        );
    }

    /**
     * @Then /^its tax included in price should be ([^"]+)$/
     */
    public function itsTaxIncludedInPriceShouldBe(string $tax): void
    {
        $unitPromotionAdjustments = $this->responseChecker->getCollectionItemsWithValue(
            $this->getAdjustmentsResponseForOrder(),
            'type',
            AdjustmentInterface::TAX_ADJUSTMENT,
        );
        $totalTax = 0;

        foreach ($unitPromotionAdjustments as $unitPromotionAdjustment) {
            if (true === $unitPromotionAdjustment['neutral']) {
                $totalTax += $unitPromotionAdjustment['amount'];
            }
        }

        Assert::same($this->getTotalAsInt($tax), $totalTax);
    }

    /**
     * @Then I should be informed that there are no payments
     */
    public function iShouldSeeInformationAboutNoPayments(): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'payments'),
            [],
        );
    }

    /**
     * @Then /^the order "[^"]+" should have order shipping state "([^"]+)"$/
     * @Then it should have order's shipping state :orderShippingState
     */
    public function theOrderShouldHaveShippingState(string $orderShippingState): void
    {
        $ordersResponse = $this->client->index(Resources::ORDERS, forgetResponse: true);

        Assert::true(
            $this->responseChecker->hasItemWithValue($ordersResponse, 'shippingState', strtolower($orderShippingState)),
            sprintf('Order does not have %s shipping state', $orderShippingState),
        );
    }

    /**
     * @Then I should not see information about shipments
     */
    public function iShouldNotSeeInformationAboutShipping(): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'shipments'),
            [],
        );
    }

    /**
     * @Then the :productName product's unit price should be :price
     */
    public function productUnitPriceShouldBe(string $productName, string $price): void
    {
        $this->iCheckData($productName);
        $orderItem = $this->sharedStorage->get('item');
        Assert::same($this->getTotalAsInt($price), $orderItem['unitPrice']);
    }

    /**
     * @Then the :productName product's discounted unit price should be :price
     */
    public function productDiscountedUnitPriceShouldBe(string $productName, string $price): void
    {
        $orderItem = $this->sharedStorage->get('item');
        Assert::same($this->getTotalAsInt($price), $orderItem['fullDiscountedUnitPrice']);
    }

    /**
     * @Then the :productName product's quantity should be :quantity
     */
    public function productQuantityShouldBe(string $productName, int $quantity): void
    {
        $orderItem = $this->sharedStorage->get('item');
        Assert::same($quantity, $orderItem['quantity']);
    }

    /**
     * @Then the :productName product's item discount should be :price
     */
    public function productItemDiscountShouldBe(string $productName, string $price): void
    {
        $orderItem = $this->sharedStorage->get('item');

        $adjustments = $this->responseChecker->getCollectionItemsWithValue(
            $this->getAdjustmentsResponseForOrder(true),
            'type',
            AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
        );

        foreach ($adjustments as $adjustment) {
            if (in_array($adjustment['orderItemUnit'], $orderItem['units'])) {
                Assert::same($this->getTotalAsInt($price), $adjustment['amount']);

                return;
            }
        }
    }

    /**
     * @Then the :productName product's order discount should be :price
     */
    public function productOrderDiscountShouldBe(string $productName, string $price): void
    {
        $orderItem = $this->sharedStorage->get('item');

        $adjustments = $this->responseChecker->getCollectionItemsWithValue(
            $this->getAdjustmentsResponseForOrder(true),
            'type',
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
        );

        foreach ($adjustments as $adjustment) {
            if (in_array($adjustment['orderItemUnit'], $orderItem['units'])) {
                Assert::same($this->getTotalAsInt(trim($price, ' ~')), $adjustment['amount']);

                return;
            }
        }
    }

    /**
     * @Then the :productName product's subtotal should be :subTotal
     */
    public function productSubtotalShouldBe(string $productName, string $subTotal): void
    {
        $orderItem = $this->sharedStorage->get('item');
        $response = $this->getAdjustmentsResponseForOrder(true);

        $unitPromotionAdjustments = 0;
        foreach ($this->responseChecker->getCollection($response) as $adjustment) {
            if (in_array($adjustment['type'], [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT])) {
                if (in_array($adjustment['orderItemUnit'], $orderItem['units'])) {
                    $unitPromotionAdjustments += $adjustment['amount'];
                }
            }
        }

        Assert::same($this->getTotalAsInt($subTotal), $orderItem['unitPrice'] * $orderItem['quantity'] + $unitPromotionAdjustments);
    }

    /**
     * @Then I should be notified that the order has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheOrderHasBeenSuccessfullyShipped(): void
    {
        $response = $this->client->getLastResponse();
        Assert::true(
            $this->responseChecker->isAccepted($response),
            'Order could not be shipped.',
        );
    }

    /**
     * @Then it should have shipment in state shipped
     */
    public function itShouldHaveShipmentInStateShipped(): void
    {
        $shipmentIri = $this->responseChecker->getValue(
            $this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue()),
            'shipments',
        )[0];

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($shipmentIri['@id']), 'state', ShipmentInterface::STATE_SHIPPED),
            sprintf('Shipment for this order is not %s', ShipmentInterface::STATE_SHIPPED),
        );
    }

    /**
     * @Then this order should have order shipping state :orderShippingState
     */
    public function thisOrderShouldHaveOrderShippingState(string $orderShippingState): void
    {
        $ordersResponse = $this->client->index(Resources::ORDERS);

        Assert::true(
            $this->responseChecker->hasItemWithValue($ordersResponse, 'shippingState', strtolower($orderShippingState)),
            sprintf('Order does not have %s shipping state', $orderShippingState),
        );
    }

    /**
     * @Then I should not be able to ship this order
     */
    public function iShouldNotBeAbleToShipThisOrder(): void
    {
        $order = $this->sharedStorage->get('order');

        $this->client->applyTransition(
            Resources::SHIPMENTS,
            (string) $order->getShipments()->first()->getId(),
            ShipmentTransitions::TRANSITION_SHIP,
        );

        Assert::false(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Order has been shipped, but should not.',
        );
    }

    /**
     * @Then I should be informed that the order does not exist
     */
    public function iShouldBeInformedThatTheOrderDoesNotExist(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Not Found',
        );
    }

    /**
     * @Then there should be :count shipping address changes in the registry
     */
    public function thereShouldBeCountShippingAddressChangesInTheRegistry(int $count): void
    {
        $order = $this->sharedStorage->get('order');
        $response = $this->client->subResourceIndex(
            Resources::ADDRESSES,
            Subresources::ADDRESSES_LOG_ENTRIES,
            (string) $order->getShippingAddress()->getId(),
        );
        Assert::same($this->responseChecker->countCollectionItems($response), $count);
    }

    /**
     * @Then I should not be able to resend the order confirmation email
     */
    public function iShouldNotBeAbleToResendTheOrderConfirmationEmail(): void
    {
        $this->client->customItemAction(
            Resources::ORDERS,
            $this->sharedStorage->get('order')->getTokenValue(),
            HttpRequest::METHOD_POST,
            'resend-confirmation-email',
        );

        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot resend order confirmation email for order with state cancelled.',
        );
    }

    /**
     * @Then there should be :count billing address changes in the registry
     */
    public function thereShouldBeCountBillingAddressChangesInTheRegistry(int $count): void
    {
        $order = $this->sharedStorage->get('order');
        $response = $this->client->subResourceIndex(
            Resources::ADDRESSES,
            Subresources::ADDRESSES_LOG_ENTRIES,
            (string) $order->getBillingAddress()->getId(),
        );
        Assert::same($this->responseChecker->countCollectionItems($response), $count);
    }

    /**
     * @Then I should be notified that the order's payment has been successfully completed
     */
    public function iShouldBeNotifiedThatTheOrdersPaymentHasBeenSuccessfullyCompleted(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then /^I should not be able to mark (this order) as paid again$/
     */
    public function iShouldNotBeAbleToMarkThisOrderAsPaidAgain(OrderInterface $order): void
    {
        $this->client->applyTransition(
            Resources::PAYMENTS,
            (string) $order->getLastPayment()->getId(),
            PaymentTransitions::TRANSITION_COMPLETE,
        );

        Assert::false($this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that the order's payment could not be finalized due to insufficient stock
     */
    public function iShouldBeNotifiedThatTheOrdersPaymentCouldNotBeFinalizedDueToInsufficientStock(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Not enough units to decrease on hold quantity from the inventory of a variant',
        );
    }

    /**
     * @param array<string, mixed> $address
     */
    private function itShouldBeAddressedTo(
        array $address,
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        Assert::same($address['firstName'] . ' ' . $address['lastName'], $customerName);
        Assert::same($address['street'], $street);
        Assert::same($address['postcode'], $postcode);
        Assert::same($address['city'], $city);
        Assert::same($address['countryCode'], $this->getCountryCodeFromName($countryName));
    }

    private function getCountryCodeFromName(string $name): string
    {
        return array_flip(Countries::getNames())[$name];
    }

    private function getCurrencyCodeFromTotal(string $total): string
    {
        return match (true) {
            str_starts_with($total, '$') => 'USD',
            str_starts_with($total, '€') => 'EUR',
            str_starts_with($total, '£') => 'GBP',
            default => throw new \InvalidArgumentException('Unsupported currency symbol'),
        };
    }

    private function getTotalAsInt(string $total): int
    {
        if ($isMinus = str_starts_with($total, '-')) {
            $total = substr($total, 1);
        }
        $amount = (int) round((float) trim($total, '$€£') * 100, 2);

        if ($isMinus) {
            return $amount * -1;
        }

        return $amount;
    }

    private function getAdjustmentsResponseForOrder(bool $forgetResponse = false): Response
    {
        $orderToken = $this->sharedStorage->get('order')->getTokenValue();

        return $this->client->subResourceIndex(
            Resources::ORDERS,
            Resources::ADJUSTMENTS,
            (string) $orderToken,
            forgetResponse: $forgetResponse,
        );
    }
}
