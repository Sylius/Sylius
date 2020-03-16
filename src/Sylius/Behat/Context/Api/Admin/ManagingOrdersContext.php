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
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
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
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->client->show($order->getNumber());
    }

    /**
     * @When I cancel this order
     */
    public function iCancelThisOrder(): void
    {
        $this->client->applyTransition(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'number'),
            OrderTransitions::TRANSITION_CANCEL
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
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show($this->sharedStorage->get('order')->getNumber()),
                'state',
                strtolower($state)
            ),
            sprintf('Order have different state then %s', $state)
        );
    }

    /**
     * @Then it should have shipment in state :state
     */
    public function itShouldHaveShipmentState(string $state): void
    {
        $shipmentIri = $this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'shipments')[0];

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($shipmentIri), 'state', strtolower($state)),
            sprintf('Shipment for this order is not %s', $state)
        );
    }

    /**
     * @Then it should have payment state :state
     */
    public function itShouldHavePaymentState($state): void
    {
        $paymentIri = $this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'payments')[0];

        Assert::true(
            $this->responseChecker->hasValue($this->client->showByIri($paymentIri), 'state', strtolower($state)),
            sprintf('payment for this order is not %s', $state)
        );
    }

    /**
     * @Then /^there should be(?:| only) (\d+) payments?$/
     */
    public function theOrderShouldHaveNumberOfPayments(int $number): void
    {
        Assert::eq(count($this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'payments')), $number);
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
     * @Then I should not be able to cancel this order
     */
    public function iShouldNotBeAbleToCancelThisOrder(): void
    {
        $this->iCancelThisOrder();
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Transition "cancel" cannot be applied on state "cancelled"'
        );
    }

    /**
     * @Then /^the order's total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersTotalShouldBe(string $total): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'total'),
            $this->getPriceFromString($total)
        );
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersPromotionTotalShouldBe(string $promotionTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show($this->sharedStorage->get('order')->getNumber()), 'orderPromotionTotal'),
            $this->getPriceFromString($promotionTotal)
        );
    }

    private function getPriceFromString(string $price): int
    {
        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }
}
