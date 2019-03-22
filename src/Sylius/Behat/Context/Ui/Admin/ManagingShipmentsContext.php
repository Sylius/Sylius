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
use Sylius\Behat\Page\Admin\Shipment\IndexPageInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
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
     * @Then I ship shipment order :orderNumber
     */
    public function iShipShipmentOrder(string $orderNumber): void
    {
        $this->indexPage->shipShipmentWithSpecificOrderNumber($orderNumber);
    }

    /**
     * @Given I should see order :orderNumber as :shippingState
     */
    public function iShouldSeeOrderAs(string $orderNumber, string $shippingState): void
    {
        $order = $this->indexPage->getRowWithSpecificOrderNumber($orderNumber);
        Assert::same($shippingState, $order);
    }
}
