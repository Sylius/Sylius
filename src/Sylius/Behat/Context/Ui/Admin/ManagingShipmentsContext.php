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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Page\Admin\Shipment\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var ShowPageInterface */
    private $orderShowPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(IndexPageInterface $indexPage, ShowPageInterface $orderShowPage, NotificationCheckerInterface $notificationChecker)
    {
        $this->indexPage = $indexPage;
        $this->orderShowPage = $orderShowPage;
        $this->notificationChecker = $notificationChecker;
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
        Channel $channel = null
    ): void {
        $parameters = [
            'number' => $orderNumber,
            'state' => $shippingState,
            'customer' => $customer->getEmail(),
        ];

        if ($channel !== null) {
            $parameters = ['channel' => $channel->getCode()];
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
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @Then I should see :count shipment(s) in the list
     * @Then I should see a single shipment in the list
     */
    public function iShouldSeeCountShipmentsInList(int $count = 1): void
    {
        Assert::same($count, $this->indexPage->countItems());
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
     * @Then I should see order page with details of order :order
     */
    public function iShouldSeeOrderPageWithDetailsOfOrder(OrderInterface $order): void
    {
        Assert::true($this->orderShowPage->isOpen(['id' => $order->getId()]));
    }
}
