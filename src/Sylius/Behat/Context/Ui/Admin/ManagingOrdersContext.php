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
use Sylius\Behat\Page\Admin\Order\HistoryPageInterface;
use Sylius\Behat\Page\Admin\Order\IndexPageInterface;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Page\Admin\Order\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

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
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var HistoryPageInterface
     */
    private $historyPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var SharedSecurityServiceInterface
     */
    private $sharedSecurityService;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        ShowPageInterface $showPage,
        UpdatePageInterface $updatePage,
        HistoryPageInterface $historyPage,
        NotificationCheckerInterface $notificationChecker,
        SharedSecurityServiceInterface $sharedSecurityService
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
        $this->updatePage = $updatePage;
        $this->historyPage = $historyPage;
        $this->notificationChecker = $notificationChecker;
        $this->sharedSecurityService = $sharedSecurityService;
    }

    /**
     * @Given I am browsing orders
     * @When I browse orders
     */
    public function iBrowseOrders(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I browse order's :order history
     */
    public function iBrowseOrderHistory(OrderInterface $order): void
    {
        $this->historyPage->open(['id' => $order->getId()]);
    }

    /**
     * @Given /^I am viewing the summary of (this order)$/
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->showPage->open(['id' => $order->getId()]);
    }

    /**
     * @When /^I mark (this order) as paid$/
     */
    public function iMarkThisOrderAsAPaid(OrderInterface $order): void
    {
        $this->showPage->completeOrderLastPayment($order);
    }

    /**
     * @When /^I mark (this order)'s payment as refunded$/
     */
    public function iMarkThisOrderSPaymentAsRefunded(OrderInterface $order): void
    {
        $this->showPage->refundOrderLastPayment($order);
    }

    /**
     * @When specify its tracking code as :trackingCode
     */
    public function specifyItsTrackingCodeAs($trackingCode): void
    {
        $this->showPage->specifyTrackingCode($trackingCode);
        $this->sharedStorage->set('tracking_code', $trackingCode);
    }

    /**
     * @When /^I ship (this order)$/
     */
    public function iShipThisOrder(OrderInterface $order): void
    {
        $this->showPage->shipOrder($order);
    }

    /**
     * @When I switch the way orders are sorted by :fieldName
     */
    public function iSwitchSortingBy($fieldName): void
    {
        $this->indexPage->sortBy($fieldName);
    }

    /**
     * @When I specify filter date from as :dateTime
     */
    public function iSpecifyFilterDateFromAs($dateTime): void
    {
        $this->indexPage->specifyFilterDateFrom($dateTime);
    }

    /**
     * @When I specify filter date to as :dateTime
     */
    public function iSpecifyFilterDateToAs($dateTime): void
    {
        $this->indexPage->specifyFilterDateTo($dateTime);
    }

    /**
     * @When I choose :channelName as a channel filter
     */
    public function iChooseChannelAsAChannelFilter($channelName): void
    {
        $this->indexPage->chooseChannelFilter($channelName);
    }

    /**
     * @When I choose :currencyName as the filter currency
     */
    public function iChooseCurrencyAsTheFilterCurrency($currencyName): void
    {
        $this->indexPage->chooseCurrencyFilter($currencyName);
    }

    /**
     * @When I specify filter total being greater than :total
     */
    public function iSpecifyFilterTotalBeingGreaterThan($total): void
    {
        $this->indexPage->specifyFilterTotalGreaterThan($total);
    }

    /**
     * @When I specify filter total being less than :total
     */
    public function iSpecifyFilterTotalBeingLessThan($total): void
    {
        $this->indexPage->specifyFilterTotalLessThan($total);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @Then I should see a single order from customer :customer
     */
    public function iShouldSeeASingleOrderFromCustomer(CustomerInterface $customer): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['customer' => $customer->getEmail()]));
    }

    /**
     * @Then it should have been placed by the customer :customerEmail
     */
    public function itShouldBePlacedByCustomer($customerEmail): void
    {
        Assert::true($this->showPage->hasCustomer($customerEmail));
    }

    /**
     * @Then it should be shipped to :customerName, :street, :postcode, :city, :countryName
     * @Then /^(this order) should (?:|still )be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function itShouldBeShippedTo(
        OrderInterface $order = null,
        $customerName,
        $street,
        $postcode,
        $city,
        $countryName
    ): void {
        if (null !== $order) {
            $this->iSeeTheOrder($order);
        }

        Assert::true($this->showPage->hasShippingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then it should be billed to :customerName, :street, :postcode, :city, :countryName
     * @Then the order should be billed to :customerName, :street, :postcode, :city, :countryName
     * @Then /^(this order) bill should (?:|still )be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function itShouldBeBilledTo(
        OrderInterface $order = null,
        $customerName,
        $street,
        $postcode,
        $city,
        $countryName
    ): void {
        if (null !== $order) {
            $this->iSeeTheOrder($order);
        }

        Assert::true($this->showPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then it should be shipped via the :shippingMethodName shipping method
     */
    public function itShouldBeShippedViaShippingMethod($shippingMethodName): void
    {
        Assert::true($this->showPage->hasShipment($shippingMethodName));
    }

    /**
     * @Then it should be paid with :paymentMethodName
     */
    public function itShouldBePaidWith($paymentMethodName): void
    {
        Assert::true($this->showPage->hasPayment($paymentMethodName));
    }

    /**
     * @Then /^it should have (\d+) items$/
     * @Then I should see :amount orders in the list
     * @Then I should see a single order in the list
     */
    public function itShouldHaveAmountOfItems($amount = 1): void
    {
        Assert::same($this->showPage->countItems(), (int) $amount);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList($productName): void
    {
        Assert::true($this->showPage->isProductInTheList($productName));
    }

    /**
     * @Then the order's items total should be :itemsTotal
     */
    public function theOrdersItemsTotalShouldBe($itemsTotal): void
    {
        Assert::eq($this->showPage->getItemsTotal(), $itemsTotal);
    }

    /**
     * @Then /^the order's total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersTotalShouldBe($total): void
    {
        Assert::eq($this->showPage->getTotal(), $total);
    }

    /**
     * @Then there should be a shipping charge :shippingCharge
     */
    public function theOrdersShippingChargesShouldBe($shippingCharge): void
    {
        Assert::true($this->showPage->hasShippingCharge($shippingCharge));
    }

    /**
     * @Then the order's shipping total should be :shippingTotal
     */
    public function theOrdersShippingTotalShouldBe($shippingTotal): void
    {
        Assert::eq($this->showPage->getShippingTotal(), $shippingTotal);
    }

    /**
     * @Then the order's payment should (also) be :paymentAmount
     */
    public function theOrdersPaymentShouldBe($paymentAmount): void
    {
        Assert::eq($this->showPage->getPaymentAmount(), $paymentAmount);
    }

    /**
     * @Then the order should have tax :tax
     */
    public function theOrderShouldHaveTax($tax): void
    {
        Assert::true($this->showPage->hasTax($tax));
    }

    /**
     * @Then /^the order's tax total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersTaxTotalShouldBe($taxTotal): void
    {
        Assert::eq($this->showPage->getTaxTotal(), $taxTotal);
    }

    /**
     * @Then the order's promotion discount should be :promotionDiscount
     */
    public function theOrdersPromotionDiscountShouldBe($promotionDiscount): void
    {
        Assert::true($this->showPage->hasPromotionDiscount($promotionDiscount));
    }

    /**
     * @Then the order's shipping promotion should be :promotion
     */
    public function theOrdersShippingPromotionDiscountShouldBe($promotionData): void
    {
        Assert::same($this->showPage->getShippingPromotionData(), $promotionData);
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersPromotionTotalShouldBe($promotionTotal): void
    {
        Assert::eq($this->showPage->getPromotionTotal(), $promotionTotal);
    }

    /**
     * @When I check :itemName data
     */
    public function iCheckData($itemName): void
    {
        $this->sharedStorage->set('item', $itemName);
    }

    /**
     * @Then /^(its) code should be "([^"]+)"$/
     */
    public function itemCodeShouldBe($itemName, $code): void
    {
        Assert::same($this->showPage->getItemCode($itemName), $code);
    }

    /**
     * @Then /^(its) unit price should be ([^"]+)$/
     */
    public function itemUnitPriceShouldBe($itemName, $unitPrice): void
    {
        Assert::eq($this->showPage->getItemUnitPrice($itemName), $unitPrice);
    }

    /**
     * @Then /^(its) discounted unit price should be ([^"]+)$/
     */
    public function itemDiscountedUnitPriceShouldBe($itemName, $discountedUnitPrice): void
    {
        Assert::eq($this->showPage->getItemDiscountedUnitPrice($itemName), $discountedUnitPrice);
    }

    /**
     * @Then /^(its) quantity should be ([^"]+)$/
     */
    public function itemQuantityShouldBe($itemName, $quantity): void
    {
        Assert::eq($this->showPage->getItemQuantity($itemName), $quantity);
    }

    /**
     * @Then /^(its) subtotal should be ([^"]+)$/
     */
    public function itemSubtotalShouldBe($itemName, $subtotal): void
    {
        Assert::eq($this->showPage->getItemSubtotal($itemName), $subtotal);
    }

    /**
     * @Then /^(its) discount should be ([^"]+)$/
     */
    public function theItemShouldHaveDiscount($itemName, $discount): void
    {
        Assert::eq($this->showPage->getItemDiscount($itemName), $discount);
    }

    /**
     * @Then /^(its) tax should be ([^"]+)$/
     */
    public function itemTaxShouldBe($itemName, $tax): void
    {
        Assert::eq($this->showPage->getItemTax($itemName), $tax);
    }

    /**
     * @Then /^(its) total should be ([^"]+)$/
     */
    public function itemTotalShouldBe($itemName, $total): void
    {
        Assert::eq($this->showPage->getItemTotal($itemName), $total);
    }

    /**
     * @Then I should be notified that the order's payment has been successfully completed
     */
    public function iShouldBeNotifiedThatTheOrderSPaymentHasBeenSuccessfullyCompleted(): void
    {
        $this->notificationChecker->checkNotification(
            'Payment has been successfully updated.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that the order's payment has been successfully refunded
     */
    public function iShouldBeNotifiedThatTheOrderSPaymentHasBeenSuccessfullyRefunded(): void
    {
        $this->notificationChecker->checkNotification(
            'Payment has been successfully refunded.',
            NotificationType::success()
        );
    }

    /**
     * @Then it should have payment state :paymentState
     * @Then it should have payment with state :paymentState
     */
    public function itShouldHavePaymentState($paymentState): void
    {
        Assert::true($this->showPage->hasPayment($paymentState));
    }

    /**
     * @Then it should have order's payment state :orderPaymentState
     */
    public function itShouldHaveOrderPaymentState($orderPaymentState): void
    {
        Assert::same($this->showPage->getPaymentState(), $orderPaymentState);
    }

    /**
     * @Then it should have order's shipping state :orderShippingState
     */
    public function itShouldHaveOrderShippingState($orderShippingState): void
    {
        Assert::same($this->showPage->getShippingState(), $orderShippingState);
    }

    /**
     * @Then it's payment state should be refunded
     */
    public function orderPaymentStateShouldBeRefunded(): void
    {
        Assert::same($this->showPage->getPaymentState(), 'Refunded');
    }

    /**
     * @Then /^I should not be able to mark (this order) as paid again$/
     */
    public function iShouldNotBeAbleToFinalizeItsPayment(OrderInterface $order): void
    {
        Assert::false($this->showPage->canCompleteOrderLastPayment($order));
    }

    /**
     * @Then I should be notified that the order has been successfully shipped
     */
    public function iShouldBeNotifiedThatTheOrderHasBeenSuccessfullyShipped(): void
    {
        $this->notificationChecker->checkNotification(
            'Shipment has been successfully updated.',
            NotificationType::success()
        );
    }

    /**
     * @Then /^I should not be able to ship (this order)$/
     */
    public function iShouldNotBeAbleToShipThisOrder(OrderInterface $order): void
    {
        Assert::false($this->showPage->canShipOrder($order));
    }

    /**
     * @When I cancel this order
     */
    public function iCancelThisOrder(): void
    {
        $this->showPage->cancelOrder();
    }

    /**
     * @Then I should be notified that it has been successfully updated
     */
    public function iShouldBeNotifiedAboutItHasBeenSuccessfullyCanceled(): void
    {
        $this->notificationChecker->checkNotification(
            'Order has been successfully updated.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should not be able to cancel this order
     */
    public function iShouldNotBeAbleToCancelThisOrder(): void
    {
        Assert::false($this->showPage->hasCancelButton());
    }

    /**
     * @Then this order should have state :state
     * @Then its state should be :state
     */
    public function itsStateShouldBe($state): void
    {
        Assert::same($this->showPage->getOrderState(), $state);
    }

    /**
     * @Then it should( still) have a :state state
     */
    public function itShouldHaveState($state): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['state' => $state]));
    }

    /**
     * @Then /^(the administrator) should know about (this additional note) for (this order made by "[^"]+")$/
     */
    public function theCustomerServiceShouldKnowAboutThisAdditionalNotes(
        AdminUserInterface $user,
        $note,
        OrderInterface $order
    ): void {
        $this->sharedSecurityService->performActionAsAdminUser(
            $user,
            function () use ($note, $order): void {
                $this->showPage->open(['id' => $order->getId()]);

                Assert::true($this->showPage->hasNote($note));
            }
        );
    }

    /**
     * @Then I should see an order with :orderNumber number
     */
    public function iShouldSeeOrderWithNumber($orderNumber): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see an order with :orderNumber number
     */
    public function iShouldNotSeeOrderWithNumber($orderNumber): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see any orders with currency :currencyCode
     */
    public function iShouldNotSeeAnyOrderWithCurrency($currencyCode): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['currencyCode' => $currencyCode]));
    }

    /**
     * @Then the first order should have number :number
     */
    public function theFirstOrderShouldHaveNumber($number): void
    {
        Assert::eq($this->indexPage->getColumnFields('number')[0], $number);
    }

    /**
     * @Then it should have shipment in state :shipmentState
     */
    public function itShouldHaveShipmentState($shipmentState): void
    {
        Assert::true($this->showPage->hasShipment($shipmentState));
    }

    /**
     * @Then order :orderNumber should have shipment state :shippingState
     */
    public function thisOrderShipmentStateShouldBe($shippingState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['shippingState' => $shippingState]));
    }

    /**
     * @Then the order :order should have order payment state :orderPaymentState
     * @Then /^(this order) should have order payment state "([^"]+)"$/
     */
    public function theOrderShouldHavePaymentState(OrderInterface $order, $orderPaymentState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['paymentState' => $orderPaymentState]));
    }

    /**
     * @Then the order :order should have order shipping state :orderShippingState
     * @Then /^(this order) should have order shipping state "([^"]+)"$/
     */
    public function theOrderShouldHaveShippingState(OrderInterface $order, $orderShippingState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['shippingState' => $orderShippingState]));
    }

    /**
     * @Then /^there should be(?:| only) (\d+) payments?$/
     */
    public function theOrderShouldHaveNumberOfPayments($number): void
    {
        Assert::same($this->showPage->getPaymentsCount(), (int) $number);
    }

    /**
     * @Then I should see the order :orderNumber with total :total
     */
    public function iShouldSeeTheOrderWithTotal($orderNumber, $total): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['total' => $total]));
    }

    /**
     * @When /^I want to modify a customer's (?:billing|shipping) address of (this order)$/
     */
    public function iWantToModifyACustomerSShippingAddress(OrderInterface $order): void
    {
        $this->updatePage->open(['id' => $order->getId()]);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I specify their (?:|new )shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheirShippingAddressAsFor(AddressInterface $address): void
    {
        $this->updatePage->specifyShippingAddress($address);
    }

    /**
     * @When /^I specify their (?:|new )billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheirBillingAddressAsFor(AddressInterface $address): void
    {
        $this->updatePage->specifyBillingAddress($address);
    }

    /**
     * @Then /^I should be notified that the "([^"]+)", the "([^"]+)", the "([^"]+)" and the "([^"]+)" in (shipping|billing) details are required$/
     */
    public function iShouldBeNotifiedThatTheAndTheInShippingDetailsAreRequired($firstElement, $secondElement, $thirdElement, $fourthElement, $type): void
    {
        $this->assertElementValidationMessage($type, $firstElement, sprintf('Please enter %s.', $firstElement));
        $this->assertElementValidationMessage($type, $secondElement, sprintf('Please enter %s.', $secondElement));
        $this->assertElementValidationMessage($type, $thirdElement, sprintf('Please enter %s.', $thirdElement));
        $this->assertElementValidationMessage($type, $fourthElement, sprintf('Please enter %s.', $fourthElement));
    }

    /**
     * @Then I should see :provinceName as province in the shipping address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress($provinceName): void
    {
        Assert::true($this->showPage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName ad province in the billing address
     */
    public function iShouldSeeAdProvinceInTheBillingAddress($provinceName): void
    {
        Assert::true($this->showPage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then /^(the administrator) should know about IP address of (this order made by "[^"]+")$/
     */
    public function theAdministratorShouldKnowAboutIPAddressOfThisOrderMadeBy(
        AdminUserInterface $user,
        OrderInterface $order
    ): void {
        $this->sharedSecurityService->performActionAsAdminUser(
            $user,
            function () use ($order): void {
                $this->showPage->open(['id' => $order->getId()]);

                Assert::notSame($this->showPage->getIpAddressAssigned(), '');
            }
        );
    }

    /**
     * @When /^I (clear old billing address) information$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address): void
    {
        $this->updatePage->specifyBillingAddress($address);
    }

    /**
     * @When /^I (clear old shipping address) information$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address): void
    {
        $this->updatePage->specifyShippingAddress($address);
    }

    /**
     * @When /^I do not specify new information$/
     */
    public function iDoNotSpecifyNewInformation(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then /^(the administrator) should see that (order placed by "[^"]+") has "([^"]+)" currency$/
     */
    public function theAdministratorShouldSeeThatThisOrderHasBeenPlacedIn(AdminUserInterface $user, OrderInterface $order, $currency): void
    {
        $this->sharedSecurityService->performActionAsAdminUser($user, function () use ($order, $currency): void {
            $this->showPage->open(['id' => $order->getId()]);

            Assert::same($this->showPage->getOrderCurrency(), $currency);
        });
    }

    /**
     * @Then /^(the administrator) should see the order with total "([^"]+)" in order list$/
     */
    public function theAdministratorShouldSeeTheOrderWithTotalInOrderList(AdminUserInterface $user, $total): void
    {
        $this->sharedSecurityService->performActionAsAdminUser($user, function () use ($total): void {
            $this->indexPage->open();

            Assert::true($this->indexPage->isSingleResourceOnPage(['total' => $total]));
        });
    }

    /**
     * @Then there should be :count changes in the registry
     */
    public function thereShouldBeCountChangesInTheRegistry($count): void
    {
        Assert::same($this->historyPage->countShippingAddressChanges(), (int) $count);
    }

    /**
     * @Then I should not be able to refund this payment
     */
    public function iShouldNotBeAbleToRefundThisPayment(): void
    {
        Assert::false($this->showPage->hasRefundButton());
    }

    /**
     * @Then I should not see information about payments
     */
    public function iShouldNotSeeInformationAboutPayments(): void
    {
        Assert::same($this->showPage->getPaymentsCount(), 0);
    }

    /**
     * @Then I should not see information about shipments
     */
    public function iShouldNotSeeInformationAboutShipments(): void
    {
        Assert::same($this->showPage->getShipmentsCount(), 0);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertElementValidationMessage(string $type, string $element, string $expectedMessage): void
    {
        $element = sprintf('%s_%s', $type, str_replace(' ', '_', $element));
        Assert::true($this->updatePage->checkValidationMessageFor($element, $expectedMessage));
    }
}
