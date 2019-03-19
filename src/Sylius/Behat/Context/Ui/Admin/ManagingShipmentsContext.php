<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;

final class ManagingShipmentsContext implements Context
{

    /** @var IndexPageInterface */
    private $indexPage;


    /**
     * @When I browse shipments
     */
    public function BrowseShipments()
    {

    }

    /**
     * @Then I should see two shipments in the list
     */
    public function iShouldSeeTwoShipmentsInTheList()
    {
        throw new PendingException();
    }

    /**
     * @Given the shipment of the \#00000001 order should be "([^"]*)"
     */
    public function theShipmentOfTheOrderShouldBe($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given the shipment of the \#00000002 order should be "([^"]*)"
     */
    public function theShipmentOfTheOrderShouldBe1($arg1, $arg2)
    {
        throw new PendingException();
    }
}
