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
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
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
     * @Then I should see( only) :count shipment(s) in the list
     * @Then I should see a single shipment in the list
     */
    public function iShouldSeeCountShipmentsInList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then /^I should see the shipment of (order "[^"]+") as "([^"]+)"$/
     */
    public function iShouldSeeTheShipmentOfOrderAs(OrderInterface $order, string $shippingState): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->getLastResponse(), [
                'order' => $this->iriConverter->getIriFromItem($order),
                'state' => strtolower($shippingState)
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
            $orderIri = $shipment['order'];
            $orderShowResponse = $this->client->showByIri($orderIri);

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
}
