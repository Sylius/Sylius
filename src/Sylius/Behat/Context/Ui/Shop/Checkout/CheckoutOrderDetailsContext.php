<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CheckoutOrderDetailsContext implements Context
{
    /**
     * @var ShowPageInterface
     */
    private $orderDetails;

    /**
     * @param ShowPageInterface $orderDetails
     */
    public function __construct(ShowPageInterface $orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }

    /**
     * @When /^I want to browse order details for (this order)$/
     */
    public function iWantToBrowseOrderDetailsForThisOrder(OrderInterface $order)
    {
        $this->orderDetails->open(['tokenValue' => $order->getTokenValue()]);
    }

    /**
     * @When I try to pay with :paymentMethodName payment method
     */
    public function iChangePaymentMethodTo($paymentMethodName)
    {
        $this->orderDetails->choosePaymentMethod($paymentMethodName);
        $this->orderDetails->pay();
    }

    /**
     * @Then I should be able to pay (again)
     */
    public function iShouldBeAbleToPay()
    {
        Assert::true($this->orderDetails->hasPayAction());
    }

    /**
     * @Then I should not be able to pay (again)
     */
    public function iShouldNotBeAbleToPay()
    {
        Assert::false($this->orderDetails->hasPayAction());
    }
}
