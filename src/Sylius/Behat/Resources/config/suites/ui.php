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

use Behat\Config\Config;

return (new Config())
    ->import([
        'ui/account/address_book.php',
        'ui/account/customer.php',
        'ui/account/customer_registration.php',
        'ui/account/email_verification.php',
        'ui/account/login.php',
        'ui/addressing/managing_countries.php',
        'ui/addressing/managing_zones.php',
        'ui/admin/dashboard.php',
        'ui/admin/impersonating_customers.php',
        'ui/admin/locale.php',
        'ui/admin/login.php',
        'ui/admin/panel.php',
        'ui/admin/security.php',
        'ui/cart/accessing_cart.php',
        'ui/cart/shopping_cart.php',
        'ui/channel/channels.php',
        'ui/channel/managing_channels.php',
        'ui/channel/products_accessibility_in_multiple_channels.php',
        'ui/channel/theming.php',
        'ui/checkout/checkout.php',
        'ui/checkout/paying_for_order.php',
        'ui/contact/requesting_contact.php',
        'ui/currency/currencies.php',
        'ui/currency/managing_currencies.php',
        'ui/currency/managing_exchange_rates.php',
        'ui/errors/admin/error_page.php',
        'ui/errors/shop/error_page.php',
        'ui/homepage/viewing_products.php',
        'ui/inventory/cart_inventory.php',
        'ui/inventory/checkout_inventory.php',
        'ui/inventory/displaying_inventory_on_edit_product_page.php',
        'ui/inventory/managing_inventory.php',
        'ui/locale/locales.php',
        'ui/locale/managing_locales.php',
        'ui/order/managing_orders.php',
        'ui/order/modifying_placed_order_address.php',
        'ui/order/order_history.php',
        'ui/payment/managing_payment_methods.php',
        'ui/payment/managing_payments.php',
        'ui/product/navigating_between_product_show_and_edit_pages.php',
        'ui/payment_request/payment_request_notify.php',
        'ui/product/accessing_price_history.php',
        'ui/product/adding_product_review.php',
        'ui/product/managing_product_association_types.php',
        'ui/product/managing_product_attributes.php',
        'ui/product/managing_product_options.php',
        'ui/product/managing_product_reviews.php',
        'ui/product/managing_product_variants.php',
        'ui/product/managing_products.php',
        'ui/product/viewing_product_in_admin_panel.php',
        'ui/product/viewing_product_reviews.php',
        'ui/product/viewing_products.php',
        'ui/product/viewing_price_history.php',
        'ui/product/viewing_price_history_after_catalog_promotions.php',
        'ui/promotion/applying_catalog_promotions.php',
        'ui/promotion/applying_promotion_coupon.php',
        'ui/promotion/applying_promotion_rules.php',
        'ui/promotion/managing_catalog_promotions.php',
        'ui/promotion/managing_promotion_coupons.php',
        'ui/promotion/managing_promotions.php',
        'ui/promotion/receiving_discount.php',
        'ui/promotion/removing_catalog_promotions.php',
        'ui/shipping/applying_shipping_fee.php',
        'ui/shipping/applying_shipping_method_rules.php',
        'ui/shipping/viewing_shipping_methods.php',
        'ui/shipping/managing_shipments.php',
        'ui/shipping/managing_shipping_categories.php',
        'ui/shipping/managing_shipping_methods.php',
        'ui/taxation/applying_taxes.php',
        'ui/taxation/managing_tax_categories.php',
        'ui/taxation/managing_tax_rates.php',
        'ui/taxonomy/managing_taxons.php',
        'ui/user/customer_statistics.php',
        'ui/user/managing_administrators.php',
        'ui/user/managing_customer_groups.php',
        'ui/user/managing_customers.php',
        'ui/user/managing_users.php',
    ])
;
