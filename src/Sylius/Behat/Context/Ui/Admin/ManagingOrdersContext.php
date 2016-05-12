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
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
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
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param ShowPageInterface $showPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        ShowPageInterface $showPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
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
     * @Then the :itemName should have :discount discount
     */
    public function theItemShouldHaveDiscount($itemName, $itemDiscount)
    {
        $itemDiscountOnPage = $this->showPage->getItemDiscount($itemName);

        Assert::eq(
            $itemDiscountOnPage,
            $itemDiscount,
            'Item discount is %s, but should be %s.'
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
}
