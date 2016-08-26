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

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface CompletePageInterface
{
    /**
     * @param string $productName
     * @param string $quantity
     *
     * @return bool
     */
    public function hasItemWithProductAndQuantity($productName, $quantity);

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasShippingAddress(AddressInterface $address);

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasBillingAddress(AddressInterface $address);

    /**
     * @param PaymentMethodInterface $paymentMethod
     *
     * @return bool
     */
    public function hasPaymentMethod(PaymentMethodInterface $paymentMethod);

    /**
     * @param ShippingMethodInterface $shippingMethod
     *
     * @return bool
     */
    public function hasShippingMethod(ShippingMethodInterface $shippingMethod);

    /**
     * @param ProductInterface $product
     * @param float $amount
     *
     * @return bool
     */
    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, $amount);

    /**
     * @param float $total
     * 
     * @return bool
     */
    public function hasOrderTotal($total);

    /**
     * @param string $notes
     */
    public function addNotes($notes);

    /**
     * @param string $promotionTotal
     * 
     * @return bool
     */
    public function hasPromotionTotal($promotionTotal);

    /**
     * @param string $promotionName
     * 
     * @return bool
     */
    public function hasPromotion($promotionName);

    /**
     * @param string $taxTotal
     * 
     * @return bool
     */
    public function hasTaxTotal($taxTotal);

    /**
     * @param string $price
     * 
     * @return bool
     */
    public function hasShippingTotal($price);

    /**
     * @param ProductInterface $product
     * @param string $price
     *
     * @return bool
     */
    public function hasProductUnitPrice(ProductInterface $product, $price);

    public function hasProductOutOfStockValidationMessage(ProductInterface $product);

    public function changeAddress();

    public function changeShippingMethod();

    public function changePaymentMethod();

    public function confirmOrder();
}
