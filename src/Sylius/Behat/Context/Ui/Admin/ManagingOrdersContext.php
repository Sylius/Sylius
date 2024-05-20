<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Order\IndexPageInterface;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Page\Admin\Order\UpdatePageInterface;
use Sylius\Behat\Page\ErrorPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingOrdersContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private IndexPageInterface $indexPage,
        private ShowPageInterface $showPage,
        private UpdatePageInterface $updatePage,
        private ErrorPageInterface $errorPage,
        private NotificationCheckerInterface $notificationChecker,
        private SharedSecurityServiceInterface $sharedSecurityService,
    ) {
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
     * @Given /^I am viewing the summary of (this order)$/
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->showPage->open(['id' => $order->getId()]);
    }

    /**
     * @When /^I try to view the summary of the (customer's latest cart)$/
     */
    public function iTryToViewTheSummaryOfTheCustomersLatestCart(OrderInterface $cart): void
    {
        $this->showPage->tryToOpen(['id' => $cart->getId()]);
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
    public function specifyItsTrackingCodeAs(string $trackingCode): void
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
    public function iSwitchSortingBy(string $fieldName): void
    {
        $this->indexPage->sortBy($fieldName);
    }

    /**
     * @When I specify filter date from as :dateTime
     */
    public function iSpecifyFilterDateFromAs(string $dateTime): void
    {
        $this->indexPage->specifyFilterDateFrom($dateTime);
    }

    /**
     * @When I specify filter date to as :dateTime
     */
    public function iSpecifyFilterDateToAs(string $dateTime): void
    {
        $this->indexPage->specifyFilterDateTo($dateTime);
    }

    /**
     * @When I choose :channelName as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(string $channelName): void
    {
        $this->indexPage->chooseChannelFilter($channelName);
    }

    /**
     * @When I choose :methodName as a shipping method filter
     */
    public function iChooseMethodAsAShippingMethodFilter(string $methodName): void
    {
        $this->indexPage->chooseShippingMethodFilter($methodName);
    }

    /**
     * @When I choose :currencyName as the filter currency
     */
    public function iChooseCurrencyAsTheFilterCurrency(string $currencyName): void
    {
        $this->indexPage->chooseCurrencyFilter($currencyName);
    }

    /**
     * @When I specify filter total being greater than :total
     */
    public function iSpecifyFilterTotalBeingGreaterThan(string $total): void
    {
        $this->indexPage->specifyFilterTotalGreaterThan($total);
    }

    /**
     * @When I specify filter total being less than :total
     */
    public function iSpecifyFilterTotalBeingLessThan(string $total): void
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
     * @When I filter by product :productName
     * @When I filter by products :firstProduct and :secondProduct
     */
    public function iFilterByProduct(string ...$productsNames): void
    {
        foreach ($productsNames as $productName) {
            $this->indexPage->specifyFilterProduct($productName);
        }

        $this->iFilter();
    }

    /**
     * @When I filter by variant :variantName
     * @When I filter by variants :firstVariant and :secondVariant
     */
    public function iFilterByVariant(string ...$variantsNames): void
    {
        foreach ($variantsNames as $variantName) {
            $this->indexPage->specifyFilterVariant($variantName);
        }

        $this->iFilter();
    }

    /**
     * @When I resend the order confirmation email
     */
    public function iResendTheOrderConfirmationEmail(): void
    {
        $this->showPage->resendOrderConfirmationEmail();
    }

    /**
     * @When I resend the shipment confirmation email
     */
    public function iResendTheShipmentConfirmationEmail(): void
    {
        $this->showPage->resendShipmentConfirmationEmail();
    }

    /**
     * @When I change the :addressType country to :country
     */
    public function iChangeTheCountryTo(string $addressType, string $country): void
    {
        match ($addressType) {
            'shipping' => $this->updatePage->changeShippingCountry($country),
            'billing' => $this->updatePage->changeBillingCountry($country),
            default => throw new \InvalidArgumentException(sprintf('Address type "%s" is not supported.', $addressType)),
        };
    }

    /**
     * @Then I should see a single order from customer :customer
     */
    public function iShouldSeeASingleOrderFromCustomer(CustomerInterface $customer): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['customer' => $customer->getEmail()]));
    }

    /**
     * @Then I should not be able to resend the shipment confirmation email
     */
    public function iShouldNotBeAbleToResendTheShipmentConfirmationEmail(): void
    {
        Assert::false(
            $this->showPage->isResendShipmentConfirmationEmailButtonVisible(),
            'Resend shipment confirmation email button should not be visible.',
        );
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->indexPage->countItems(), 1);
    }

    /**
     * @Then it should have been placed by the customer :customerEmail
     */
    public function itShouldBePlacedByCustomer(string $customerEmail): void
    {
        Assert::true($this->showPage->hasCustomer($customerEmail));
    }

    /**
     * @Then it should be shipped to :customerName, :street, :postcode, :city, :countryName
     */
    public function itShouldBeShippedToCustomerAtAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        $this->itShouldBeShippedTo(null, $customerName, $street, $postcode, $city, $countryName);
    }

    /**
     * @Then /^(this order) should (?:|still )be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function itShouldBeShippedTo(
        ?OrderInterface $order,
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        if (null !== $order) {
            $this->iSeeTheOrder($order);
        }

        Assert::true($this->showPage->hasShippingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then it should be billed to :customerName, :street, :postcode, :city, :countryName
     * @Then the order should be billed to :customerName, :street, :postcode, :city, :countryName
     */
    public function itShouldBeBilledToCustomerAtAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        Assert::true($this->showPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then /^(?:it|this order) should(?:| still) have "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" as its(?:| new) billing address$/
     */
    public function itShouldHaveAsItsBillingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): void {
        $this->iSeeTheOrder($this->sharedStorage->get('order'));

        Assert::true($this->showPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then I should be able to choose the :province province for the :addressType address
     */
    public function iShouldBeAbleToChooseTheProvinceForTheAddressType(ProvinceInterface $province, string $addressType): void
    {
        Assert::inArray(
            $province->getName(),
            match ($addressType) {
                'billing' => $this->updatePage->getAvailableProvincesForBillingAddress(),
                'shipping' => $this->updatePage->getAvailableProvincesForShippingAddress(),
                default => [],
            },
        );
    }

    /**
     * @Then it should have no shipping address set
     */
    public function itShouldHaveNoShippingAddressSet(): void
    {
        Assert::false($this->showPage->hasShippingAddressVisible());
    }

    /**
     * @Then it should be shipped via the :shippingMethodName shipping method
     */
    public function itShouldBeShippedViaShippingMethod(string $shippingMethodName): void
    {
        Assert::true($this->showPage->hasShipment($shippingMethodName));
    }

    /**
     * @Then it should be paid with :paymentMethodName
     */
    public function itShouldBePaidWith(string $paymentMethodName): void
    {
        Assert::true($this->showPage->hasPayment($paymentMethodName));
    }

    /**
     * @Then /^it should have (\d+) items$/
     * @Then I should see :amount orders in the list
     */
    public function itShouldHaveAmountOfItems(int $amount = 1): void
    {
        Assert::same($this->showPage->countItems(), $amount);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList(string $productName): void
    {
        Assert::true($this->showPage->isProductInTheList($productName));
    }

    /**
     * @Then the order's items total should be :itemsTotal
     */
    public function theOrdersItemsTotalShouldBe(string $itemsTotal): void
    {
        Assert::eq($this->showPage->getItemsTotal(), $itemsTotal);
    }

    /**
     * @Then /^the order's total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersTotalShouldBe(string $total): void
    {
        Assert::eq($this->showPage->getTotal(), $total);
    }

    /**
     * @Then there should be a shipping charge :shippingCharge for :shippingMethodName method
     */
    public function thereShouldBeAShippingChargeForMethod(string $shippingCharge, string $shippingMethodName): void
    {
        Assert::true($this->showPage->hasShippingCharge($shippingCharge, $shippingMethodName));
    }

    /**
     * @Then there should be a shipping tax :shippingTax for :shippingMethodName method
     */
    public function thereShouldBeAShippingTaxForMethod(string $shippingTax, string $shippingMethodName): void
    {
        Assert::true($this->showPage->hasShippingTax($shippingTax, $shippingMethodName));
    }

    /**
     * @Then the order's shipping total should be :shippingTotal
     */
    public function theOrdersShippingTotalShouldBe(string $shippingTotal): void
    {
        Assert::eq($this->showPage->getShippingTotal(), $shippingTotal);
    }

    /**
     * @Then the order's payment should (also) be :paymentAmount
     */
    public function theOrdersPaymentShouldBe(string $paymentAmount): void
    {
        Assert::eq($this->showPage->getPaymentAmount(), $paymentAmount);
    }

    /**
     * @Then the order should have tax :tax
     */
    public function theOrderShouldHaveTax(string $tax): void
    {
        Assert::true($this->showPage->hasTax($tax));
    }

    /**
     * @Then /^the order's tax total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersTaxTotalShouldBe(string $taxTotal): void
    {
        Assert::eq($this->showPage->getTaxTotal(), $taxTotal);
    }

    /**
     * @Then the order's promotion discount should be :promotionAmount from :promotionName promotion
     */
    public function theOrdersPromotionDiscountShouldBeFromPromotion(string $promotionAmount, string $promotionName): void
    {
        Assert::true($this->showPage->hasPromotionDiscount($promotionName, $promotionAmount));
    }

    /**
     * @Then the order's shipping promotion should be :promotion
     */
    public function theOrdersShippingPromotionDiscountShouldBe(string $promotionData): void
    {
        Assert::same($this->showPage->getShippingPromotionData(), $promotionData);
    }

    /**
     * @Then /^the order's promotion total should(?:| still) be "([^"]+)"$/
     */
    public function theOrdersPromotionTotalShouldBe(string $promotionTotal): void
    {
        Assert::same($this->showPage->getOrderPromotionTotal(), $promotionTotal);
    }

    /**
     * @When I check :itemName data
     */
    public function iCheckData(string $itemName): void
    {
        $this->sharedStorage->set('item', $itemName);
    }

    /**
     * @Then /^(its) code should be "([^"]+)"$/
     */
    public function itemCodeShouldBe(string $itemName, string $code): void
    {
        Assert::same($this->showPage->getItemCode($itemName), $code);
    }

    /**
     * @Then /^(its) unit price should be ([^"]+)$/
     */
    public function itemUnitPriceShouldBe(string $itemName, string $unitPrice): void
    {
        Assert::eq($this->showPage->getItemUnitPrice($itemName), $unitPrice);
    }

    /**
     * @Then /^(its) discounted unit price should be ([^"]+)$/
     */
    public function itemDiscountedUnitPriceShouldBe(string $itemName, string $discountedUnitPrice): void
    {
        Assert::eq($this->showPage->getItemDiscountedUnitPrice($itemName), $discountedUnitPrice);
    }

    /**
     * @Then /^(its) quantity should be ([^"]+)$/
     */
    public function itemQuantityShouldBe(string $itemName, int $quantity): void
    {
        Assert::eq($this->showPage->getItemQuantity($itemName), $quantity);
    }

    /**
     * @Then /^(its) subtotal should be ([^"]+)$/
     */
    public function itemSubtotalShouldBe(string $itemName, string $subtotal): void
    {
        Assert::eq($this->showPage->getItemSubtotal($itemName), $subtotal);
    }

    /**
     * @Then /^(its) discount should be ([^"]+)$/
     */
    public function theItemShouldHaveDiscount(string $itemName, string $discount): void
    {
        Assert::eq($this->showPage->getItemDiscount($itemName), $discount);
    }

    /**
     * @Then /^(its) tax should be ([^"]+)$/
     */
    public function itemTaxShouldBe(string $itemName, string $tax): void
    {
        Assert::eq($this->showPage->getItemTax($itemName), $tax);
    }

    /**
     * @Then /^(its) tax included in price should be ([^"]+)$/
     */
    public function itsTaxIncludedInPriceShouldBe(string $itemName, string $tax): void
    {
        Assert::same($this->showPage->getItemTaxIncludedInPrice($itemName), $tax);
    }

    /**
     * @Then /^(its) total should be ([^"]+)$/
     */
    public function itemTotalShouldBe(string $itemName, string $total): void
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
            NotificationType::success(),
        );
    }

    /**
     * @Then I should be notified that the order's payment could not be finalized due to insufficient stock
     */
    public function iShouldBeNotifiedThatTheOrderSPaymentCouldNotBeFinalizedDueToInsufficientStock(): void
    {
        $this->notificationChecker->checkNotification(
            'The payment cannot be completed due to insufficient stock of the',
            NotificationType::failure(),
        );
    }

    /**
     * @Then I should be notified that the order's payment has been successfully refunded
     */
    public function iShouldBeNotifiedThatTheOrderSPaymentHasBeenSuccessfullyRefunded(): void
    {
        $this->notificationChecker->checkNotification(
            'Payment has been successfully refunded.',
            NotificationType::success(),
        );
    }

    /**
     * @Then it should have payment state :paymentState
     * @Then it should have payment with state :paymentState
     */
    public function itShouldHavePaymentState(string $paymentState): void
    {
        Assert::true($this->showPage->hasPaymentWithState($paymentState));
    }

    /**
     * @Then it should have order's payment state :orderPaymentState
     */
    public function itShouldHaveOrderPaymentState(string $orderPaymentState): void
    {
        Assert::same($this->showPage->getPaymentState(), $orderPaymentState);
    }

    /**
     * @Then it should have order's shipping state :orderShippingState
     */
    public function itShouldHaveOrderShippingState(string $orderShippingState): void
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
            'Shipment has been successfully shipped.',
            NotificationType::success(),
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
            NotificationType::success(),
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
    public function itsStateShouldBe(string $state): void
    {
        Assert::same($this->showPage->getOrderState(), $state);
    }

    /**
     * @Then it should( still) have a :state state
     */
    public function itShouldHaveState(string $state): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['state' => $state]));
    }

    /**
     * @Then /^(the administrator) should know about (this additional note) for (this order made by "[^"]+")$/
     */
    public function theCustomerServiceShouldKnowAboutThisAdditionalNotes(
        AdminUserInterface $user,
        string $note,
        OrderInterface $order,
    ): void {
        $this->sharedSecurityService->performActionAsAdminUser(
            $user,
            function () use ($note, $order) {
                $this->showPage->open(['id' => $order->getId()]);

                Assert::true($this->showPage->hasNote($note));
            },
        );
    }

    /**
     * @Then I should see an order with :orderNumber number
     */
    public function iShouldSeeOrderWithNumber(string $orderNumber): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see an order with :orderNumber number
     */
    public function iShouldNotSeeOrderWithNumber(string $orderNumber): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see any orders with currency :currencyCode
     */
    public function iShouldNotSeeAnyOrderWithCurrency(string $currencyCode): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['currencyCode' => $currencyCode]));
    }

    /**
     * @Then the first order should have number :number
     */
    public function theFirstOrderShouldHaveNumber(string $number): void
    {
        Assert::eq($this->indexPage->getColumnFields('number')[0], $number);
    }

    /**
     * @Then it should have shipment in state :shipmentState
     */
    public function itShouldHaveShipmentState(string $shipmentState): void
    {
        Assert::true($this->showPage->hasShipmentWithState($shipmentState));
    }

    /**
     * @Then order :orderNumber should have shipment state :shippingState
     */
    public function thisOrderShipmentStateShouldBe(string $shippingState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['shippingState' => $shippingState]));
    }

    /**
     * @Then the order :order should have order payment state :orderPaymentState
     * @Then /^(this order) should have order payment state "([^"]+)"$/
     */
    public function theOrderShouldHavePaymentState(OrderInterface $order, string $orderPaymentState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['paymentState' => $orderPaymentState]));
    }

    /**
     * @Then the order :order should have order shipping state :orderShippingState
     * @Then /^(this order) should have order shipping state "([^"]+)"$/
     */
    public function theOrderShouldHaveShippingState(OrderInterface $order, string $orderShippingState): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['shippingState' => $orderShippingState]));
    }

    /**
     * @Then /^there should be(?:| only) (\d+) payments?$/
     */
    public function theOrderShouldHaveNumberOfPayments(string $number): void
    {
        Assert::same($this->showPage->getPaymentsCount(), (int) $number);
    }

    /**
     * @Then I should see the order :orderNumber with total :total
     */
    public function iShouldSeeTheOrderWithTotal(string $orderNumber, int $total): void
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
     * @Then /^I should be notified that all mandatory (shipping|billing) address details are incomplete$/
     */
    public function iShouldBeNotifiedThatAllMandatoryAddressDetailsAreIncomplete(string $type): void
    {
        /** @var array<int, string> $mandatoryAddressFields */
        $mandatoryAddressFields = ['first name', 'last name', 'street', 'city', 'postcode'];

        foreach ($mandatoryAddressFields as $mandatoryAddressField) {
            $this->assertElementValidationMessage(
                $type,
                $mandatoryAddressField,
                sprintf('Please enter %s.', $mandatoryAddressField),
            );
        }

        $this->assertElementValidationMessage($type, 'country', 'Please select country.');
    }

    /**
     * @Then I should see :provinceName as province in the shipping address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress(string $provinceName): void
    {
        Assert::true($this->showPage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName as province in the billing address
     */
    public function iShouldSeeAdProvinceInTheBillingAddress(string $provinceName): void
    {
        Assert::true($this->showPage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then /^(the administrator) should know about IP address of (this order made by "[^"]+")$/
     */
    public function theAdministratorShouldKnowAboutIPAddressOfThisOrderMadeBy(
        AdminUserInterface $user,
        OrderInterface $order,
    ): void {
        $this->sharedSecurityService->performActionAsAdminUser(
            $user,
            function () use ($order) {
                $this->showPage->open(['id' => $order->getId()]);

                Assert::notSame($this->showPage->getIpAddressAssigned(), '');
            },
        );
    }

    /**
     * @When /^I (clear the billing address) information$/
     */
    public function iClearTheBillingAddressInformation(AddressInterface $address): void
    {
        $this->updatePage->specifyBillingAddress($address);
    }

    /**
     * @When /^I (clear the shipping address) information$/
     */
    public function iClearTheShippingAddressInformation(AddressInterface $address): void
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
    public function theAdministratorShouldSeeThatThisOrderHasBeenPlacedIn(
        AdminUserInterface $user,
        OrderInterface $order,
        string $currency,
    ): void {
        $this->sharedSecurityService->performActionAsAdminUser($user, function () use ($order, $currency) {
            $this->showPage->open(['id' => $order->getId()]);

            Assert::same($this->showPage->getOrderCurrency(), $currency);
        });
    }

    /**
     * @Then /^(the administrator) should see the order with total "([^"]+)" in order list$/
     */
    public function theAdministratorShouldSeeTheOrderWithTotalInOrderList(AdminUserInterface $user, int $total): void
    {
        $this->sharedSecurityService->performActionAsAdminUser($user, function () use ($total) {
            $this->indexPage->open();

            Assert::true($this->indexPage->isSingleResourceOnPage(['total' => $total]));
        });
    }

    /**
     * @Then I should not be able to refund this payment
     */
    public function iShouldNotBeAbleToRefundThisPayment(): void
    {
        Assert::false($this->showPage->hasRefundButton());
    }

    /**
     * @Then I should not see information about shipments
     */
    public function iShouldNotSeeInformationAboutShipments(): void
    {
        Assert::same($this->showPage->getShipmentsCount(), 0);
    }

    /**
     * @Then the :productName product's unit price should be :price
     */
    public function productUnitPriceShouldBe(string $productName, string $price): void
    {
        Assert::same($this->showPage->getItemUnitPrice($productName), $price);
    }

    /**
     * @Then the :productName product's item discount should be :price
     */
    public function productItemDiscountShouldBe(string $productName, string $price): void
    {
        Assert::same($this->showPage->getItemDiscount($productName), $price);
    }

    /**
     * @Then the :productName product's order discount should be :price
     */
    public function productOrderDiscountShouldBe(string $productName, string $price): void
    {
        Assert::same($this->showPage->getItemOrderDiscount($productName), $price);
    }

    /**
     * @Then the :productName product's quantity should be :quantity
     */
    public function productQuantityShouldBe(string $productName, string $quantity): void
    {
        Assert::same($this->showPage->getItemQuantity($productName), $quantity);
    }

    /**
     * @Then the :productName product's subtotal should be :subTotal
     */
    public function productSubtotalShouldBe(string $productName, string $subTotal): void
    {
        Assert::same($this->showPage->getItemSubtotal($productName), $subTotal);
    }

    /**
     * @Then the :productName product's discounted unit price should be :price
     */
    public function productDiscountedUnitPriceShouldBe(string $productName, string $price): void
    {
        Assert::same($this->showPage->getItemDiscountedUnitPrice($productName), $price);
    }

    /**
     * @Then I should be informed that there are no payments
     */
    public function iShouldSeeInformationAboutNoPayments(): void
    {
        Assert::same($this->showPage->getPaymentsCount(), 0);
        Assert::true($this->showPage->hasInformationAboutNoPayment());
    }

    /**
     * @Then /^I should be notified that the (order|shipment) confirmation email has been successfully resent to the customer$/
     */
    public function iShouldBeNotifiedThatTheOrderConfirmationEmailHasBeenSuccessfullyResentToTheCustomer(string $type): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('%s confirmation has been successfully resent to the customer.', ucfirst($type)),
            NotificationType::success(),
        );
    }

    /**
     * @Then I should not be able to resend the order confirmation email
     */
    public function iShouldNotBeAbleToResendTheOrderConfirmationEmail(): void
    {
        Assert::false(
            $this->showPage->isResendOrderConfirmationEmailButtonVisible(),
            'Resend order confirmation email button should not be visible.',
        );
    }

    /**
     * @Then I should see the shipping date as :dateTime
     */
    public function iShouldSeeTheShippingDateAs(string $dateTime): void
    {
        Assert::same($this->showPage->getShippedAtDate(), $dateTime);
    }

    /**
     * @Then I should be informed that the order does not exist
     */
    public function iShouldBeInformedThatTheOrderDoesNotExist(): void
    {
        Assert::same($this->errorPage->getCode(), 404);
    }

    /**
     * @param string $type
     * @param string $element
     * @param string $expectedMessage
     *
     * @throws \InvalidArgumentException
     */
    private function assertElementValidationMessage(string $type, string $element, string $expectedMessage): void
    {
        $element = sprintf('%s_%s', $type, str_replace(' ', '_', $element));
        Assert::true($this->updatePage->checkValidationMessageFor($element, $expectedMessage));
    }
}
