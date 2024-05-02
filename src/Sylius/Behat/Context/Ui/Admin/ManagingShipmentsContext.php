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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface as OrderShowPageInterface;
use Sylius\Behat\Page\Admin\Shipment\IndexPageInterface;
use Sylius\Behat\Page\Admin\Shipment\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private OrderShowPageInterface $orderShowPage,
        private NotificationCheckerInterface $notificationChecker,
        private ShowPageInterface $showPage,
    ) {
    }

    /**
     * @When I browse shipments
     */
    public function iBrowseShipments(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then the shipment of the :orderNumber order should be :shippingState for :customer
     * @Then the shipment of the :orderNumber order should be :shippingState for :customer in :channel channel
     */
    public function shipmentOfOrderShouldBe(
        string $orderNumber,
        string $shippingState,
        CustomerInterface $customer,
        ?Channel $channel = null,
    ): void {
        $parameters = [
            'number' => $orderNumber,
            'state' => $shippingState,
            'customer' => $customer->getEmail(),
        ];

        if ($channel !== null) {
            $parameters = ['channel' => $channel->getName()];
        }

        Assert::true($this->indexPage->isSingleResourceOnPage($parameters));
    }

    /**
     * @When I choose :shipmentState as a shipment state
     */
    public function iChooseShipmentState(string $shipmentState): void
    {
        $this->indexPage->chooseStateToFilter($shipmentState);
    }

    /**
     * @When I choose :channelName as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(string $channelName): void
    {
        $this->indexPage->chooseChannelFilter($channelName);
    }

    /**
     * @When I choose :shippingMethodName as a shipping method filter
     */
    public function iChooseAsAShippingMethodFilter(string $shippingMethodName): void
    {
        $this->indexPage->chooseShippingMethodFilter($shippingMethodName);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @When I view the first shipment of the order :order
     */
    public function iViewTheShipmentOfTheOrder(OrderInterface $order): void
    {
        $this->showPage->open(['id' => $order->getShipments()->first()->getId()]);
    }

    /**
     * @Then I should see( only) :count shipment(s) in the list
     * @Then I should see a single shipment in the list
     */
    public function iShouldSeeCountShipmentsInList(int $count = 1): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then I should see a shipment of order :orderNumber
     */
    public function iShouldSeeShipmentWithOrderNumber(string $orderNumber): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see a shipment of order :orderNumber
     */
    public function iShouldNotSeeShipmentWithOrderNumber(string $orderNumber): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @When I ship the shipment of order :orderNumber
     */
    public function iShipShipmentOfOrder(string $orderNumber): void
    {
        $this->indexPage->shipShipmentOfOrderWithNumber($orderNumber);
    }

    /**
     * @Then I should see the shipment of order :orderNumber as :shippingState
     */
    public function iShouldSeeTheShipmentOfOrderAs(string $orderNumber, string $shippingState): void
    {
        Assert::same($shippingState, $this->indexPage->getShipmentStatusByOrderNumber($orderNumber));
    }

    /**
     * @Then I should be notified that the shipment has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheShipmentHasBeenSuccessfullyShipped(): void
    {
        $this->notificationChecker->checkNotification('Shipment has been successfully shipped.', NotificationType::success());
    }

    /**
     * @When I move to the details of first shipment's order
     */
    public function iMoveToDetailsOfFirstShipment(): void
    {
        $this->indexPage->showOrderPageForNthShipment(1);
    }

    /**
     * @When I ship the shipment of order :orderNumber with :trackingCode tracking code
     */
    public function iShipTheShipmentOfOrderWithTrackingCode(string $orderNumber, string $trackingCode): void
    {
        $this->indexPage->shipShipmentOfOrderWithTrackingCode($orderNumber, $trackingCode);
    }

    /**
     * @Then I should see order page with details of order :order
     * @Then I should see the details of order :order
     */
    public function iShouldSeeOrderPageWithDetailsOfOrder(OrderInterface $order): void
    {
        Assert::true($this->orderShowPage->isOpen(['id' => $order->getId()]));
    }

    /**
     * @Then /^I should see shipment for (the "[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeeShipmentForTheOrderInTheList(string $orderNumber, int $position): void
    {
        Assert::true($this->indexPage->isShipmentWithOrderNumberInPosition($orderNumber, $position));
    }

    /**
     * @Then I should see :amount :product units in the list
     */
    public function iShouldSeeUnitsInTheList(int $amount, string $productName): void
    {
        Assert::same($this->showPage->getAmountOfUnits($productName), $amount);
    }

    /**
     * @Then I should see the shipment of order :orderNumber shipped at :dateTime
     */
    public function iShouldSeeTheShippingDateAs(string $orderNumber, string $dateTime): void
    {
        Assert::same($this->indexPage->getShippedAtDate($orderNumber), $dateTime);
    }
}
