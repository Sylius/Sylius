<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
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
    public function BrowseShipments(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see single shipment in the list
     */
    public function iShouldSeeSingleShipmentInList(): void
    {
        Assert::eq(1, $this->indexPage->countItems());
    }

    /**
     * @Then the shipment of the :orderNumber order should be :shippingState
     */
    public function ShipmentOfOrderShouldBe(string $orderNumber, string $shippingState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['order_number' => $orderNumber, 'state' =>$shippingState]));
    }
}
