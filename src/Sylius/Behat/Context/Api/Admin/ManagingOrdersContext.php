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
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;
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
    ) {
    }

    /**
     * @Given /^I am viewing the summary of (this order)$/
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $response = $this->client->show(Resources::ORDERS, $order->getTokenValue());
        Assert::same($this->responseChecker->getValue($response, '@id'), $this->iriConverter->getIriFromResource($order));

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
     * @When I specify filter date to as :dateTime
     */
    public function iSpecifyFilterDateToAs(string $dateTime): void
    {
        $this->client->addFilter('checkoutCompletedAt[before]', $dateTime);
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
        $this->client->applyTransition(
            Resources::SHIPMENTS,
            (string) $order->getShipments()->first()->getId(),
            ShipmentTransitions::TRANSITION_SHIP,
        );
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
     * @Then it should have shipment in state :state
     */
    public function itShouldHaveShipmentState(string $state): void
    {
        $shipmentIri = $this->responseChecker->getValue(
            $this->client->show(Resources::ORDERS, $this->sharedStorage->get('order')->getTokenValue()),
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
            'Transition "cancel" cannot be applied',
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
        return (int) round((float) trim($total, '$€£') * 100, 2);
    }
}
