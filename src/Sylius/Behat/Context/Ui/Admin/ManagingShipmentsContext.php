<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
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
     * @Then I should see :count shipments in the list
     */
    public function iShouldSeeCountShipmentsInList(int $count): void
    {
        Assert::same($count, $this->indexPage->countItems());
    }
}
