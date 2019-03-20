<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;

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
    public function iShouldSeeTwoShipmentsInTheList()
    {
        throw new PendingException();
    }

    /**
     * @Then the shipment of the :orderNumber order should be :shippingState
     */
    public function theShipmentOfTheOrderShouldBe($arg1, $arg2)
    {
        throw new PendingException();
    }
}
