<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingOrdersContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param ShowPageInterface $showPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        ShowPageInterface $showPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I browse orders
     */
    public function iBrowseOrders()
    {
        $this->indexPage->open();
    }

    /**
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order)
    {
        $this->showPage->open(['id' => $order->getId()]);
    }

    /**
     * @When /^I mark (this order) as a paid$/
     */
    public function iMarkThisOrderAsAPaid(OrderInterface $order)
    {
        $this->showPage->completeOrderLastPayment($order);
    }

    /**
     * @When specify its tracking code as :trackingCode
     */
    public function specifyItsTrackingCodeAs($trackingCode)
    {
        $this->showPage->specifyTrackingCode($trackingCode);
    }

    /**
     * @Given /^I ship (this order)$/
     */
    public function iShipThisOrder(OrderInterface $order)
    {
        $this->showPage->shipOrder($order);
    }

    /**
     * @Then I should see a single order from customer :customer
     */
    public function iShouldSeeASingleOrderFromCustomer(CustomerInterface $customer)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['customer' => $customer->getEmail()]),
            sprintf('Cannot find order for customer "%s" in the list.', $customer->getEmail())
        );
    }

    /**
     * @Then it should have been placed by the customer :customerEmail
     */
    public function itShouldBePlacedByCustomer($customerEmail)
    {
        Assert::true(
            $this->showPage->hasCustomer($customerEmail),
            sprintf('Cannot find customer "%s".', $customerEmail)
        );
    }

    /**
     * @Then it should be shipped to :customerName, :street, :postcode, :city, :countryName
     */
    public function itShouldBeShippedTo($customerName, $street, $postcode, $city, $countryName)
    {
        Assert::true(
            $this->showPage->hasShippingAddress($customerName, $street, $postcode, $city, $countryName),
            sprintf('Cannot find shipping address "%s, %s %s, %s".', $street, $postcode, $city, $countryName)
        );
    }

    /**
     * @Then it should be billed to :customerName, :street, :postcode, :city, :countryName
     */
    public function itShouldBeBilledTo($customerName, $street, $postcode, $city, $countryName)
    {
        Assert::true(
            $this->showPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName),
            sprintf('Cannot find shipping address "%s, %s %s, %s".', $street, $postcode, $city, $countryName)
        );
    }

    /**
     * @Then it should be shipped via the :shippingMethodName shipping method
     */
    public function itShouldBeShippedViaShippingMethod($shippingMethodName)
    {
        Assert::true(
            $this->showPage->hasShipment($shippingMethodName),
            sprintf('Cannot find shipment "%s".', $shippingMethodName)
        );
    }

    /**
     * @Then it should be paid with :paymentMethodName
     */
    public function itShouldBePaidWith($paymentMethodName)
    {
        Assert::true(
            $this->showPage->hasPayment($paymentMethodName),
            sprintf('Cannot find payment "%s".', $paymentMethodName)
        );
    }

    /**
     * @Then /^it should have (\d+) items$/
     */
    public function itShouldHaveAmountOfItems($amount)
    {
        $itemsCount = $this->showPage->countItems();

        Assert::eq(
            $amount,
            $itemsCount,
            sprintf('There should be %d items, but get %d.', $amount, $itemsCount)
        );
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList($productName)
    {
        Assert::true(
            $this->showPage->isProductInTheList($productName),
            sprintf('Product %s is not in the item list.', $productName)
        );
    }

    /**
     * @Then the order's items total should be :itemsTotal
     */
    public function theOrdersItemsTotalShouldBe($itemsTotal)
    {
        $itemsTotalOnPage = $this->showPage->getItemsTotal();

        Assert::eq(
            $itemsTotalOnPage,
            $itemsTotal,
            'Items total is %s, but should be %s.'
        );
    }

    /**
     * @Then the order's total should be :total
     */
    public function theOrdersTotalShouldBe($total)
    {
        $totalOnPage = $this->showPage->getTotal();

        Assert::eq(
            $totalOnPage,
            $total,
            'Total is %s, but should be %s.'
        );
    }

    /**
     * @Then there should be a shipping charge :shippingCharge
     */
    public function theOrdersShippingChargesShouldBe($shippingCharge)
    {
        Assert::true(
            $this->showPage->hasShippingCharge($shippingCharge),
            sprintf('Shipping charges is not "%s".', $shippingCharge)
        );
    }

    /**
     * @Then the order's shipping total should be :shippingTotal
     */
    public function theOrdersShippingTotalShouldBe($shippingTotal)
    {
        $shippingTotalOnPage = $this->showPage->getShippingTotal();

        Assert::eq(
            $shippingTotal,
            $shippingTotalOnPage,
            sprintf('Shipping total is "%s", but should be "%s".', $shippingTotalOnPage, $shippingTotal)
        );
    }

    /**
     * @Then the order's tax total should be :taxTotal
     */
    public function theOrdersTaxTotalShouldBe($taxTotal)
    {
        $taxTotalOnPage = $this->showPage->getTaxTotal();

        Assert::eq(
            $taxTotal,
            $taxTotalOnPage,
            sprintf('Tax total is "%s", but should be "%s".', $taxTotalOnPage, $taxTotal)
        );
    }

    /**
     * @Then the order's promotion discount should be :promotionDiscount
     */
    public function theOrdersPromotionDiscountShouldBe($promotionDiscount)
    {
        Assert::true(
            $this->showPage->hasPromotionDiscount($promotionDiscount),
            sprintf('Promotion discount is not "%s".', $promotionDiscount)
        );
    }

    /**
     * @Then the order's promotion total should be :promotionTotal
     */
    public function theOrdersPromotionTotalShouldBe($promotionTotal)
    {
        $promotionTotalOnPage = $this->showPage->getPromotionTotal();

        Assert::eq(
            $promotionTotalOnPage,
            $promotionTotal,
            'Promotion total is %s, but should be %s.'
        );
    }

    /**
     * @When I check :itemName data
     */
    public function iCheckData($itemName)
    {
        $this->sharedStorage->set('item', $itemName);
    }

    /**
     * @Then /^(its) unit price should be ([^"]+)$/
     */
    public function itemUnitPriceShouldBe($itemName, $unitPrice)
    {
        $itemUnitPriceOnPage = $this->showPage->getItemUnitPrice($itemName);

        Assert::eq(
            $itemUnitPriceOnPage,
            $unitPrice,
            'Item unit price is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) discounted unit price should be ([^"]+)$/
     */
    public function itemDiscountedUnitPriceShouldBe($itemName, $discountedUnitPrice)
    {
        $itemUnitPriceOnPage = $this->showPage->getItemDiscountedUnitPrice($itemName);

        Assert::eq(
            $itemUnitPriceOnPage,
            $discountedUnitPrice,
            'Item discounted unit price is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) quantity should be ([^"]+)$/
     */
    public function itemQuantityShouldBe($itemName, $quantity)
    {
        $itemQuantityOnPage = $this->showPage->getItemQuantity($itemName);

        Assert::eq(
            $itemQuantityOnPage,
            $quantity,
            'Item quantity is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) subtotal should be ([^"]+)$/
     */
    public function itemSubtotalShouldBe($itemName, $subtotal)
    {
        $itemSubtotalOnPage = $this->showPage->getItemSubtotal($itemName);

        Assert::eq(
            $itemSubtotalOnPage,
            $subtotal,
            'Item subtotal is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) discount should be ([^"]+)$/
     * @Then the :itemName should have :discount discount
     */
    public function theItemShouldHaveDiscount($itemName, $discount)
    {
        $itemDiscountOnPage = $this->showPage->getItemDiscount($itemName);

        Assert::eq(
            $itemDiscountOnPage,
            $discount,
            'Item discount is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) tax should be ([^"]+)$/
     */
    public function itemTaxShouldBe($itemName, $tax)
    {
        $itemTaxOnPage = $this->showPage->getItemTax($itemName);

        Assert::eq(
            $itemTaxOnPage,
            $tax,
            'Item tax is %s, but should be %s.'
        );
    }

    /**
     * @Then /^(its) total should be ([^"]+)$/
     */
    public function itemTotalShouldBe($itemName, $total)
    {
        $itemTotalOnPage = $this->showPage->getItemTotal($itemName);

        Assert::eq(
            $itemTotalOnPage,
            $total,
            'Item total is %s, but should be %s.'
        );
    }

    /**
     * @When I delete the order :order
     */
    public function iDeleteOrder(OrderInterface $order)
    {
        $this->sharedStorage->set('order', $order);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['number' => $order->getNumber()]);
    }

    /**
     * @Then /^(this order) should not exist in the registry$/
     */
    public function orderShouldNotExistInTheRegistry(OrderInterface $order)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['number' => $order->getNumber()]),
            sprintf('Order with number %s exists but should not.', $order->getNumber())
        );
    }

    /**
     * @Then I should be notified that the order's payment has been successfully completed
     */
    public function iShouldBeNotifiedThatTheOrderSPaymentHasBeenSuccessfullyCompleted()
    {
        $this->notificationChecker->checkNotification('Payment has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then it should have completed payment state
     */
    public function itShouldHaveCompletedPaymentState()
    {
        Assert::true(
            $this->showPage->hasPayment('Completed'),
            'It should have payment with completed state.'
        );
    }

    /**
     * @Then /^I should not be able to mark (this order) as paid again$/
     */
    public function iShouldNotBeAbleToFinalizeItsPayment(OrderInterface $order)
    {
        Assert::false(
            $this->showPage->canCompleteOrderLastPayment($order),
            'It should not have complete payment button.'
        );
    }

    /**
     * @Then I should be notified that the order's shipment has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheOrderSShipmentHasBeenSuccessfullyShipped()
    {
        $this->notificationChecker->checkNotification('Shipment has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then its shipment state should be :shipmentState
     */
    public function itsShipmentStateShouldBe($shipmentState)
    {
        Assert::true(
            $this->showPage->hasShipment($shipmentState),
            sprintf('It should have shipment with %s state', $shipmentState)
        );
    }

    /**
     * @Then /^I should not be able to ship (this order)$/
     */
    public function iShouldNotBeAbleToShipThisOrder(OrderInterface $order)
    {
        Assert::false(
            $this->showPage->canShipOrder($order),
            'It should not have ship shipment button.'
        );
    }

    /**
     * @When I cancel this order
     */
    public function iCancelThisOrder()
    {
        $this->showPage->cancelOrder();
    }

    /**
     * @Then I should be notified that it has been successfully updated
     */
    public function iShouldBeNotifiedAboutItHasBeenSuccessfullyCanceled()
    {
        $this->notificationChecker->checkNotification(
            'Order has been successfully updated.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should not be able to cancel this order
     */
    public function iShouldNotBeAbleToCancelThisOrder()
    {
        Assert::false(
            $this->showPage->hasCancelButton(),
            'There should not be a cancel button, but it is.'
        );
    }

    /**
     * @Then its state should be :state
     */
    public function itsStateShouldBe($state)
    {
        Assert::same(
            $this->showPage->getOrderState(),
            $state,
            'The order state should be %2$s, but it is %s.'
        );
    }
}
