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
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private SharedStorageInterface $sharedStorage,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When I browse shipments
     */
    public function iBrowseShipments(): void
    {
        $this->client->index(Resources::SHIPMENTS);
    }

    /**
     * @When I choose :state as a shipment state
     */
    public function iChooseShipmentState(string $state): void
    {
        $this->client->addFilter('state', $state);
    }

    /**
     * @When I move to the details of first shipment's order
     */
    public function iMoveToDetailsOfFirstShipment(): void
    {
        $firstShipment = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        /** @var OrderInterface $order */
        $order = $this->iriConverter->getResourceFromIri($firstShipment['order']);

        $this->client->customItemAction(Resources::ORDERS, $order->getTokenValue(), HttpRequest::METHOD_GET, 'shipments');
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->addFilter('order.channel.code', $channel->getCode());
    }

    /**
     * @When I choose :shippingMethod as a shipping method filter
     */
    public function iChooseAsAShippingMethodFilter(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->addFilter('method.code', $shippingMethod->getCode());
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
        $response = $this->client->show(Resources::SHIPMENTS, (string) $order->getShipments()->first()->getId());

        $this->sharedStorage->set('response', $response);
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
        $this->client->applyTransition(Resources::SHIPMENTS, (string) $order->getShipments()->first()->getId(), ShipmentTransitions::TRANSITION_SHIP);
    }

    /**
     * @When I try to ship the shipment of order :order
     */
    public function iTryToShipShipmentOfOrder(OrderInterface $order): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();

        $this->client->customAction(
            sprintf('%s/admin/shipments/%s/ship', $this->apiUrlPrefix, (string) $shipment->getId()),
            HttpRequest::METHOD_PATCH,
        );
    }

    /**
     * @When I ship the shipment of order :order with :trackingCode tracking code
     */
    public function iShipTheShipmentOfOrderWithTrackingCode(OrderInterface $order, string $trackingCode): void
    {
        $this->client->applyTransition(
            Resources::SHIPMENTS,
            (string) $order->getShipments()->first()->getId(),
            ShipmentTransitions::TRANSITION_SHIP,
            ['tracking' => $trackingCode],
        );
    }

    /**
     * @Then I should be notified that the shipment has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheShipmentHasBeenSuccessfullyShipped(): void
    {
        Assert::true(
            $this->responseChecker->isAccepted($this->client->getLastResponse()),
            'Shipment was not successfully shipped',
        );
    }

    /**
     * @Then I should be notified that shipment has been already shipped
     */
    public function iShouldBeNotifiedThatTheShipmentHasBeenAlreadyShipped(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'You cannot ship a shipment that was shipped before.',
            'Shipment was able to be shipped when should not.',
        );
    }

    /**
     * @Then /^I should see the shipment of (order "[^"]+") as "([^"]+)"$/
     */
    public function iShouldSeeTheShipmentOfOrderAs(OrderInterface $order, string $shippingState): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(Resources::SHIPMENTS), [
                'order' => $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
                'state' => strtolower($shippingState),
            ]),
            sprintf('Shipment for order %s with state %s does not exist', $order->getNumber(), $shippingState),
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
                $this->iriConverter->getIriFromResource($order),
            ),
            sprintf('On position %s there is no shipment for order %s', $position, $order->getNumber()),
        );
    }

    /**
     * @Then I should see the shipment of order :order shipped at :dateTime
     */
    public function iShouldSeeTheShippingDateAs(OrderInterface $order, string $dateTime): void
    {
        Assert::eq(
            new \DateTime($this->responseChecker->getValue($this->client->show(Resources::SHIPMENTS, (string) $order->getShipments()->first()->getId()), 'shippedAt')),
            new \DateTime($dateTime),
            'Shipment was shipped in different date',
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
        ?ChannelInterface $channel = null,
    ): void {
        $this->client->index(Resources::SHIPMENTS);

        foreach ($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'state',
            StringInflector::nameToLowercaseCode($shippingState),
        ) as $shipment) {
            $orderShowResponse = $this->client->showByIri($shipment['order']);

            if (!$this->responseChecker->HasValue($orderShowResponse, 'number', $orderNumber)) {
                continue;
            }

            $this->client->showByIri($this->responseChecker->getValue($orderShowResponse, 'customer'));
            if (!$this->responseChecker->HasValue($this->client->getLastResponse(), 'email', $customer->getEmail())) {
                continue;
            }

            if ($channel === null) {
                return;
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
            sprintf('There is no shipment for order %s', $order->getNumber()),
        );
    }

    /**
     * @Then I should not see a shipment of order :order
     */
    public function iShouldNotSeeShipmentWithOrderNumber(OrderInterface $order): void
    {
        Assert::false(
            $this->isShipmentForOrder($order),
            sprintf('There is shipment for order %s', $order->getNumber()),
        );
    }

    /**
     * @Then I should see :amount :product units in the list
     */
    public function iShouldSeeUnitsInTheList(int $amount, ProductInterface $product): void
    {
        $response = $this->sharedStorage->has('response') ? $this->sharedStorage->get('response') : $this->client->getLastResponse();

        $shipmentUnitsFromResponse = $this->responseChecker->getValue($response, 'units');

        $productUnitsCounter = 0;
        foreach ($shipmentUnitsFromResponse as $shipmentUnitFromResponse) {
            $shipmentUnitResponse = $this->client->showByIri($shipmentUnitFromResponse);
            $productVariantResponse = $this->client->showByIri(
                $this->responseChecker->getValue($shipmentUnitResponse, 'shippable')['@id'],
            );
            $productResponse = $this->client->showByIri(
                $this->responseChecker->getValue($productVariantResponse, 'product'),
            );

            $productName = $this->responseChecker->getValue($productResponse, 'translations')['en_US']['name'];

            if ($productName === $product->getName()) {
                ++$productUnitsCounter;
            }
        }

        Assert::same($productUnitsCounter, $amount);
    }

    /**
     * @Then I should see the details of order :order
     */
    public function iShouldSeeOrderWithDetails(OrderInterface $order): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'order',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
            ),
            sprintf('Order with number %s does not exist', $order->getNumber()),
        );
    }

    private function isShipmentForOrder(OrderInterface $order): bool
    {
        return $this->responseChecker->hasItemWithValue(
            $this->client->getLastResponse(),
            'order',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
        );
    }
}
