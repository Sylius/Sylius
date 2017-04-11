<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CompletePage extends SymfonyPage implements CompletePageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableAccessorInterface $tableAccessor
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_complete';
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithProductAndQuantity($productName, $quantity)
    {
        $table = $this->getElement('items_table');

        try {
            $this->tableAccessor->getRowWithFields($table, ['item' => $productName, 'qty' => $quantity]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingAddress(AddressInterface $address)
    {
        $shippingAddress = $this->getElement('shipping_address')->getText();

        return $this->isAddressValid($shippingAddress, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAddress(AddressInterface $address)
    {
        $billingAddress = $this->getElement('billing_address')->getText();

        return $this->isAddressValid($billingAddress, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        if (!$this->hasElement('shipping_method')) {
            return false;
        }

        return false !== strpos($this->getElement('shipping_method')->getText(), $shippingMethod->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodName()
    {
        return $this->getElement('payment_method')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPaymentMethod()
    {
        return $this->hasElement('payment_method');
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, $amount)
    {
        $columns = $this->getProductRowElement($product)->findAll('css', 'td');
        $priceWithoutDiscount = $this->getPriceFromString($columns[1]->getText());
        $priceWithDiscount = $this->getPriceFromString($columns[3]->getText());
        $discount = $priceWithoutDiscount - $priceWithDiscount;

        return $discount === $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOrderTotal($total)
    {
        if (!$this->hasElement('order_total')) {
            return false;
        }

        return $this->getTotalFromString($this->getElement('order_total')->getText()) === $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrencyOrderTotal()
    {
        return $this->getBaseTotalFromString($this->getElement('base_order_total')->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function addNotes($notes)
    {
        $this->getElement('extra_notes')->setValue($notes);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotionTotal($promotionTotal)
    {
        return false !== strpos($this->getElement('promotion_total')->getText(), $promotionTotal);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotion($promotionName)
    {
        return false !== stripos($this->getElement('promotion_discounts')->getText(), $promotionName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingPromotion($promotionName)
    {
        return false !== stripos($this->getElement('promotion_shipping_discounts')->getText(), $promotionName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxTotal($taxTotal)
    {
        return false !== strpos($this->getElement('tax_total')->getText(), $taxTotal);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingTotal($price)
    {
        return false !== strpos($this->getElement('shipping_total')->getText(), $price);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductUnitPrice(ProductInterface $product, $price)
    {
        $productRowElement = $this->getProductRowElement($product);

        return null !== $productRowElement->find('css', sprintf('td:contains("%s")', $price));
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product)
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        return $this->getElement('validation_errors')->getText() === $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationErrors()
    {
        return $this->getElement('validation_errors')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLocale($localeName)
    {
        return false !== strpos($this->getElement('locale')->getText(), $localeName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCurrency($currencyCode)
    {
        return false !== strpos($this->getElement('currency')->getText(), $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function confirmOrder()
    {
        $this->getElement('confirm_button')->press();
    }

    public function changeAddress()
    {
        $this->getElement('addressing_step_label')->click();
    }

    public function changeShippingMethod()
    {
        $this->getElement('shipping_step_label')->click();
    }

    public function changePaymentMethod()
    {
        $this->getElement('payment_step_label')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingProvinceName($provinceName)
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return false !== stripos($shippingAddressText, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingProvinceName($provinceName)
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return false !== stripos($billingAddressText, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPromotionDiscount($promotionName)
    {
        return $this->getElement('promotion_shipping_discounts')->find('css', '.description')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function tryToOpen(array $urlParameters = [])
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $start = microtime(true);
            $end = $start + 15;
            do {
                parent::tryToOpen($urlParameters);
                sleep(3);
            } while (!$this->isOpen() && microtime(true) < $end);

            return;
        }

        parent::tryToOpen($urlParameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'addressing_step_label' => '.steps a:contains("Address")',
            'base_order_total' => '#base-total',
            'billing_address' => '#sylius-billing-address',
            'confirm_button' => 'form button',
            'currency' => '#sylius-order-currency-code',
            'extra_notes' =>'#sylius_checkout_complete_notes',
            'items_table' => '#sylius-order',
            'locale' => '#sylius-order-locale-name',
            'order_total' => 'td:contains("Total")',
            'payment_method' => '#sylius-payment-method',
            'payment_step_label' => '.steps a:contains("Payment")',
            'product_row' => 'tbody tr:contains("%name%")',
            'promotion_discounts' => '#promotion-discounts',
            'promotion_shipping_discounts' => '#promotion-shipping-discounts',
            'promotion_total' => '#promotion-total',
            'shipping_address' => '#sylius-shipping-address',
            'shipping_method' => '#sylius-shipping-method',
            'shipping_step_label' => '.steps a:contains("Shipping")',
            'shipping_total' => '#shipping-total',
            'tax_total' => '#tax-total',
            'validation_errors' => '.sylius-validation-error',
        ]);
    }

    /**
     * @param ProductInterface $product
     *
     * @return NodeElement
     */
    private function getProductRowElement(ProductInterface $product)
    {
        return $this->getElement('product_row', ['%name%' => $product->getName()]);
    }

    /**
     * @param string $displayedAddress
     * @param AddressInterface $address
     *
     * @return bool
     */
    private function isAddressValid($displayedAddress, AddressInterface $address)
    {
        return
            $this->hasAddressPart($displayedAddress, $address->getCompany(), true) &&
            $this->hasAddressPart($displayedAddress, $address->getFirstName()) &&
            $this->hasAddressPart($displayedAddress, $address->getLastName()) &&
            $this->hasAddressPart($displayedAddress, $address->getPhoneNumber(), true) &&
            $this->hasAddressPart($displayedAddress, $address->getStreet()) &&
            $this->hasAddressPart($displayedAddress, $address->getCity()) &&
            $this->hasAddressPart($displayedAddress, $address->getProvinceCode(), true) &&
            $this->hasAddressPart($displayedAddress, $this->getCountryName($address->getCountryCode())) &&
            $this->hasAddressPart($displayedAddress, $address->getPostcode())
        ;
    }

    /**
     * @param string $address
     * @param string $addressPart
     *
     * @return bool
     */
    private function hasAddressPart($address, $addressPart, $optional = false)
    {
        if ($optional && null === $addressPart) {
            return true;
        }

        return false !== strpos($address, $addressPart);
    }

    /**
     * @param string $countryCode
     *
     * @return string
     */
    private function getCountryName($countryCode)
    {
        return strtoupper(Intl::getRegionBundle()->getCountryName($countryCode, 'en'));
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round(str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }

    /**
     * @param string $total
     *
     * @return int
     */
    private function getTotalFromString($total)
    {
        $total = str_replace('Total:', '', $total);

        return $this->getPriceFromString($total);
    }

    /**
     * @param string $total
     *
     * @return int
     */
    private function getBaseTotalFromString($total)
    {
        $total = str_replace('Total in base currency:', '', $total);

        return $this->getPriceFromString($total);
    }
}
