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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

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
    public function iWantToBrowseOrderDetailsForThisOrder(OrderInterface $order): void
    {
        $this->orderDetails->open(['tokenValue' => $order->getTokenValue()]);
    }

    /**
     * @When I try to pay with :paymentMethodName payment method
     */
    public function iChangePaymentMethodTo($paymentMethodName): void
    {
        $this->orderDetails->choosePaymentMethod($paymentMethodName);
        $this->orderDetails->pay();
    }

    /**
     * @Then I should be able to pay (again)
     */
    public function iShouldBeAbleToPay(): void
    {
        Assert::true($this->orderDetails->hasPayAction());
    }

    /**
     * @Then I should not be able to pay (again)
     */
    public function iShouldNotBeAbleToPay(): void
    {
        Assert::false($this->orderDetails->hasPayAction());
    }
}
