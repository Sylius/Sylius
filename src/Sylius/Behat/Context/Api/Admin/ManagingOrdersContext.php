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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
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
    ) {
    }

    /**
     * @Given /^I am viewing the summary of (this order)$/
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->client->show(Resources::ORDERS, $order->getTokenValue());
    }

    /**
     * @When I browse orders
     */
    public function iBrowseOrders(): void
    {
        $this->client->index(Resources::ORDERS);
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
                $this->iriConverter->getIriFromItem($customer),
            ),
            sprintf('There is no order for customer %s', $customer->getEmail()),
        );
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), 1);
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
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'total'),
            $total,
        );
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersPromotionTotalShouldBe(int $promotionTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'orderPromotionTotal'),
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
}
