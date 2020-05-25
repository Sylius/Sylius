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
use Sylius\Behat\Client\ApiIriClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiIriClientInterface */
    private $iriClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ApiIriClientInterface $iriClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->iriClient = $iriClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When I browse shipments
     */
    public function iBrowseShipments(): void
    {
        $this->client->index();
    }

    /**
     * @When I choose :state as a shipment state
     */
    public function iChooseShipmentState(string $state): void
    {
        $this->client->addFilter('state', $state);
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->addFilter('order.channel.code', $channel->getCode());
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();
    }

    /**
     * @When I view the first shipment of the order :order
     */
    public function iViewTheShipmentOfTheOrder(OrderInterface $order): void
    {
        $this->client->show((string) $order->getShipments()->first()->getId());
    }

    /**
     * @Then I should see( only) :count shipment(s) in the list
     * @Then I should see a single shipment in the list
     */
    public function iShouldSeeCountShipmentsInList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @When I ship the shipment of order :order
     */
    public function iShipShipmentOfOrder(OrderInterface $order): void
    {
        $this->client->applyTransition((string) $order->getShipments()->first()->getId(), ShipmentTransitions::TRANSITION_SHIP);
    }

    /**
     * @When I ship the shipment of order :order with :trackingCode tracking code
     */
    public function iShipTheShipmentOfOrderWithTrackingCode(OrderInterface $order, string $trackingCode): void
    {
        $this->client->applyTransition(
            (string) $order->getShipments()->first()->getId(),
            ShipmentTransitions::TRANSITION_SHIP,
            ['tracking' => $trackingCode]
        );
    }

    /**
     * @Then I should be notified that the shipment has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheShipmentHasBeenSuccessfullyShipped(): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'state'), 'shipped', 'Shipment is not shipped');
    }

    /**
     * @Then /^I should see the shipment of (order "[^"]+") as "([^"]+)"$/
     */
    public function iShouldSeeTheShipmentOfOrderAs(OrderInterface $order, string $shippingState): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(), [
                'order' => $this->iriConverter->getIriFromItem($order),
                'state' => strtolower($shippingState),
            ]),
            sprintf('Shipment for order %s with state %s does not exist', $order->getNumber(), $shippingState)
        );
    }

    /**
     * @Then /^I should see shipment for the ("[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeeShipmentForTheOrderInTheList(OrderInterface $order, int $position): void
    {
        Assert::true(
            $this->responseChecker->hasItemOnPositionWithValue(
                $this->client->getLastResponse(),
                --$position,
                'order',
                $this->iriConverter->getIriFromItem($order)
            ),
            sprintf('On position %s there is no shipment for order %s', $position, $order->getNumber())
        );
    }

    /**
     * @Then I should see the shipment of order :order shipped at :dateTime
     */
    public function iShouldSeeTheShippingDateAs(OrderInterface $order, string $dateTime): void
    {
        Assert::eq(
             new \DateTime($this->responseChecker->getValue($this->client->show((string) $order->getShipments()->first()->getId()), 'shippedAt')),
             new \DateTime($dateTime),
             'Shipment was shipped in different date'
         );
    }

    /**
     * @Then the shipment of the :orderNumber order should be :shippingState for :customer
     * @Then the shipment of the :orderNumber order should be :shippingState for :customer in :channel channel
     */
    public function shipmentOfOrderShouldBe(
        string $orderNumber,
        string $shippingState,
        CustomerInterface $customer,
        ChannelInterface $channel = null
    ): void {
        $this->client->index();

        foreach ($this->responseChecker->getCollectionItemsWithValue($this->client->getLastResponse(), 'state',
            StringInflector::nameToLowercaseCode($shippingState)) as $shipment) {
            $orderShowResponse = $this->client->showByIri($shipment['order']);

            if (!$this->responseChecker->HasValue($orderShowResponse, 'number', $orderNumber)) {
                continue;
            }

            $this->client->showByIri($this->responseChecker->getValue($orderShowResponse, 'customer'));
            if (!$this->responseChecker->HasValue($this->client->getLastResponse(), 'email', $customer->getEmail())) {
                continue;
            }
            $this->client->showByIri($this->responseChecker->getValue($orderShowResponse, 'channel'));
            if ($this->responseChecker->HasValue($this->client->getLastResponse(), 'name', $channel->getName())) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no shipment with given data');
    }

    /**
     * @Then I should see a shipment of order :order
     */
    public function iShouldSeeShipmentWithOrderNumber(OrderInterface $order): void
    {
        Assert::true(
            $this->isShipmentForOrder($order),
            sprintf('There is no shipment for order %s', $order->getNumber())
        );
    }

    /**
     * @Then I should not see a shipment of order :order
     */
    public function iShouldNotSeeShipmentWithOrderNumber(OrderInterface $order): void
    {
        Assert::false(
            $this->isShipmentForOrder($order),
            sprintf('There is shipment for order %s', $order->getNumber())
        );
    }

    /**
     * @Then I should see :amount :product units in the list
     */
    public function iShouldSeeUnitsInTheList(int $amount, ProductInterface $product): void
    {
        $shipmentUnitsFromResponse = $this->responseChecker->getValue($this->client->getLastResponse(), 'units');

        $productUnitsCounter = 0;
        foreach ($shipmentUnitsFromResponse as $shipmentUnitFromResponse) {
            $shipmentUnitResponse = $this->iriClient->showByIri($shipmentUnitFromResponse);
            $productVariantResponse = $this->iriClient->showByIri(
                $this->responseChecker->getValue($shipmentUnitResponse, 'shippable')['@id']
            );
            $productResponse = $this->iriClient->showByIri(
                $this->responseChecker->getValue($productVariantResponse, 'product')
            );

            $productName = $this->responseChecker->getValue($productResponse, 'translations')['en_US']['name'];

            if ($productName === $product->getName()) {
                ++$productUnitsCounter;
            }
        }

        Assert::same($productUnitsCounter, $amount);
    }

    private function isShipmentForOrder(OrderInterface $order): bool
    {
        return $this->responseChecker->hasItemWithValue(
            $this->client->getLastResponse(),
            'order',
            $this->iriConverter->getIriFromItem($order)
        );
    }
}
