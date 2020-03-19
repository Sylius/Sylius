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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $shipmentsClient;

    /** @var ApiClientInterface */
    private $paymentsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $shipmentsClient,
        ApiClientInterface $paymentsClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->shipmentsClient = $shipmentsClient;
        $this->paymentsClient = $paymentsClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse orders
     */
    public function iBrowseOrders(): void
    {
        $this->client->index();
    }

    /**
     * @Given /^I am viewing the summary of (this order)$/
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->client->show($order->getNumber());
    }

    /**
     * @When /^I cancel (this order)$/
     */
    public function iCancelThisOrder(OrderInterface $order): void
    {
        $this->client->applyTransition(
            $this->responseChecker->getValue($this->client->show($order->getNumber()), 'number'),
            OrderTransitions::TRANSITION_CANCEL
        );
    }

    /**
     * @When /^I mark (this order) as paid$/
     */
    public function iMarkThisOrderAsAPaid(OrderInterface $order): void
    {
        $this->paymentsClient->applyTransition(
            (string) $order->getLastPayment()->getId(),
            PaymentTransitions::TRANSITION_COMPLETE
        );
    }

    /**
     * @When /^I ship (this order)$/
     */
    public function iShipThisOrder(OrderInterface $order): void
    {
        $this->shipmentsClient->applyTransition(
            (string) $order->getShipments()->first()->getId(),
            ShipmentTransitions::TRANSITION_SHIP
        );
    }

    /**
     * @Then I should see a single order from customer :customer
     */
    public function iShouldSeeASingleOrderFromCustomer(CustomerInterface $customer): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->getLastResponse(),
            'customer',
            $this->iriConverter->getIriFromItem($customer)),
            sprintf('There is no order for customer %s', $customer->getEmail())
        );
    }

    /**
     * @Then I should be notified that it has been successfully updated
     */
    public function iShouldBeNotifiedAboutItHasBeenSuccessfullyCanceled(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Resource could not be completed'
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
        $orderState = $this->responseChecker->getValue($this->client->show($order->getNumber()), 'state');

        Assert::same($orderState, strtolower($state));
    }

    /**
     * @Then it should have shipment in state :state
     */
    public function itShouldHaveShipmentState(string $state): void
    {
        $shipmentsIri = $this->responseChecker->getValue(
            $this->client->show($this->sharedStorage->get('order')->getNumber()),
            'shipments'
        );

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($shipmentsIri[0]), 'state', strtolower($state)),
            sprintf('Shipment for this order is not %s', $state)
        );
    }

    /**
     * @Then it should have payment state :state
     */
    public function itShouldHavePaymentState($state): void
    {
        $paymentsIri = $this->responseChecker->getValue(
            $this->client->show($this->sharedStorage->get('order')->getNumber()),
            'payments'
        );

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($paymentsIri[0]), 'state', strtolower($state)),
            sprintf('payment for this order is not %s', $state)
        );
    }

    /**
     * @Then /^there should be(?:| only) (\d+) payments?$/
     */
    public function theOrderShouldHaveNumberOfPayments(int $number): void
    {
        Assert::count(
            $this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'payments'),
            $number
        );
    }

    /**
     * @Then /^(this order) should have order payment state "([^"]+)"$/
     */
    public function theOrderShouldHavePaymentState(OrderInterface $order, string $paymentState): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show($order->getNumber()), 'paymentState', strtolower($paymentState)),
            sprintf('Order %s does not have %s payment state', $order->getNumber(), $paymentState)
        );
    }

    /**
     * @Then /^I should not be able to cancel (this order)$/
     */
    public function iShouldNotBeAbleToCancelThisOrder(OrderInterface $order): void
    {
        $this->iCancelThisOrder($order);
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Transition "cancel" cannot be applied'
        );
    }

    /**
     * @Then /^the order's total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersTotalShouldBe(int $total): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'total'),
            $total
        );
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be ("[^"]+")$/
     */
    public function theOrdersPromotionTotalShouldBe(int $promotionTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'orderPromotionTotal'),
            $promotionTotal
        );
    }
}
