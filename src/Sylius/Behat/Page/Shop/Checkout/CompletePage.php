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

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\RouterInterface;

class CompletePage extends SymfonyPage implements CompletePageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_complete';
    }

    public function hasItemWithProductAndQuantity(string $productName, string $quantity): bool
    {
        $table = $this->getElement('items_table');

        try {
            $this->tableAccessor->getRowWithFields($table, ['item' => $productName, 'qty' => $quantity]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function hasShippingAddress(AddressInterface $address): bool
    {
        $shippingAddress = $this->getElement('shipping_address')->getText();

        return $this->isAddressValid($shippingAddress, $address);
    }

    public function hasBillingAddress(AddressInterface $address): bool
    {
        $billingAddress = $this->getElement('billing_address')->getText();

        return $this->isAddressValid($billingAddress, $address);
    }

    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool
    {
        if (!$this->hasElement('shipping_method')) {
            return false;
        }

        return false !== strpos($this->getElement('shipping_method')->getText(), $shippingMethod->getName());
    }

    public function getPaymentMethodName(): string
    {
        return $this->getElement('payment_method')->getText();
    }

    public function hasPaymentMethod(): bool
    {
        return $this->hasElement('payment_method');
    }

    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, int $amount): bool
    {
        $columns = $this->getProductRowElement($product)->findAll('css', 'td');
        $priceWithoutDiscount = $this->getPriceFromString($columns[1]->getText());
        $priceWithDiscount = $this->getPriceFromString($columns[3]->getText());
        $discount = $priceWithoutDiscount - $priceWithDiscount;

        return $discount === $amount;
    }

    public function hasOrderTotal(int $total): bool
    {
        if (!$this->hasElement('order_total')) {
            return false;
        }

        return $this->getTotalFromString($this->getElement('order_total')->getText()) === $total;
    }

    public function getBaseCurrencyOrderTotal(): string
    {
        return (string) $this->getBaseTotalFromString($this->getElement('base_order_total')->getText());
    }

    public function addNotes(string $notes): void
    {
        $this->getElement('extra_notes')->setValue($notes);
    }

    public function hasPromotionTotal(string $promotionTotal): bool
    {
        return false !== strpos($this->getElement('promotion_total')->getText(), $promotionTotal);
    }

    public function hasPromotion(string $promotionName): bool
    {
        return false !== stripos($this->getElement('promotion_discounts')->getText(), $promotionName);
    }

    public function hasShippingPromotion(string $promotionName): bool
    {
        /** @var NodeElement $shippingPromotions */
        $shippingPromotions = $this->getElement('promotions_shipping_details');

        return false !== strpos($shippingPromotions->getAttribute('data-html'), $promotionName);
    }

    public function getTaxTotal(): string
    {
        return $this->getElement('tax_total')->getText();
    }

    public function getShippingTotal(): string
    {
        return $this->getElement('shipping_total')->getText();
    }

    public function hasShippingTotal(): bool
    {
        return $this->hasElement('shipping_total');
    }

    public function hasProductUnitPrice(ProductInterface $product, string $price): bool
    {
        $productRowElement = $this->getProductRowElement($product);

        return null !== $productRowElement->find('css', sprintf('td:contains("%s")', $price));
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        return $this->getElement('validation_errors')->getText() === $message;
    }

    public function getValidationErrors(): string
    {
        return $this->getElement('validation_errors')->getText();
    }

    public function hasLocale(string $localeName): bool
    {
        return false !== strpos($this->getElement('locale')->getText(), $localeName);
    }

    public function hasCurrency(string $currencyCode): bool
    {
        return false !== strpos($this->getElement('currency')->getText(), $currencyCode);
    }

    public function confirmOrder(): void
    {
        $this->getElement('confirm_button')->press();
    }

    public function changeAddress(): void
    {
        $this->getElement('addressing_step_label')->click();
    }

    public function changeShippingMethod(): void
    {
        $this->getElement('shipping_step_label')->click();
    }

    public function changePaymentMethod(): void
    {
        $this->getElement('payment_step_label')->click();
    }

    public function hasShippingProvinceName(string $provinceName): bool
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return false !== stripos($shippingAddressText, $provinceName);
    }

    public function hasBillingProvinceName(string $provinceName): bool
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return false !== stripos($billingAddressText, $provinceName);
    }

    public function getShippingPromotionDiscount(string $promotionName): string
    {
        return $this->getElement('promotion_shipping_discounts')->find('css', '.description')->getText();
    }

    public function hasShippingPromotionWithDiscount(string $promotionName, string $discount): bool
    {
        $promotionWithDiscount = sprintf('%s: %s', $promotionName, $discount);

        /** @var NodeElement $shippingPromotions */
        $shippingPromotions = $this->getElement('promotions_shipping_details');

        return false !== strpos($shippingPromotions->getAttribute('data-html'), $promotionWithDiscount);
    }

    public function hasOrderPromotion(string $promotionName): bool
    {
        /** @var NodeElement $shippingPromotions */
        $shippingPromotions = $this->getElement('order_promotions_details');

        return false !== strpos($shippingPromotions->getAttribute('data-html'), $promotionName);
    }

    public function tryToOpen(array $urlParameters = []): void
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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'addressing_step_label' => '[data-test-step-address]',
            'base_order_total' => '[data-test-summary-order-total]',
            'billing_address' => '[data-test-billing-address]',
            'confirm_button' => '[data-test-confirmation-button]',
            'currency' => '[data-test-order-currency-code]',
            'extra_notes' => '[data-test-extra-notes]',
            'items_table' => '[data-test-order-table]',
            'locale' => '[data-test-order-locale-name]',
            'order_promotions_details' => '#order-promotions-details',
            'order_total' => '[data-test-order-total]',
            'payment_method' => '[data-test-payment-method]',
            'payment_step_label' => '[data-test-step-payment]',
            'product_row' => '[data-test-product-row="%name%"]',
            'promotion_discounts' => '[data-test-promotion-discounts]',
            'promotions_shipping_details' => '#shipping-promotion-details',
            'promotion_shipping_discounts' => '[data-test-promotion-shipping-discounts]',
            'promotion_total' => '[data-test-promotion-total]',
            'shipping_address' => '[data-test-shipping-address]',
            'shipping_method' => '[data-test-shipping-method]',
            'shipping_step_label' => '[data-test-step-shipping]',
            'shipping_total' => '[data-test-shipping-total]',
            'tax_total' => '[data-test-tax-total]',
            'validation_errors' => '[data-test-validation-error]',
        ]);
    }

    private function getProductRowElement(ProductInterface $product): NodeElement
    {
        return $this->getElement('product_row', ['%name%' => $product->getName()]);
    }

    private function isAddressValid(string $displayedAddress, AddressInterface $address): bool
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

    private function hasAddressPart(string $address, ?string $addressPart, bool $optional = false): bool
    {
        if ($optional && null === $addressPart) {
            return true;
        }

        return false !== strpos($address, $addressPart);
    }

    private function getCountryName(string $countryCode): string
    {
        return strtoupper(Intl::getRegionBundle()->getCountryName($countryCode, 'en'));
    }

    private function getPriceFromString(string $price): int
    {
        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }

    private function getTotalFromString(string $total): int
    {
        $total = str_replace('Total:', '', $total);

        return $this->getPriceFromString($total);
    }

    private function getBaseTotalFromString(string $total): int
    {
        $total = str_replace('Total in base currency:', '', $total);

        return $this->getPriceFromString($total);
    }
}
