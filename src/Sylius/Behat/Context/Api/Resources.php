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

namespace Sylius\Behat\Context\Api;

final class Resources
{
    public const ADDRESSES = 'addresses';

    public const ADMINISTRATORS = 'administrators';

    public const AVATAR_IMAGES = 'avatar-images';

    public const CATALOG_PROMOTIONS = 'catalog-promotions';

    public const CHANNELS = 'channels';

    public const CONTACT_REQUESTS = 'contact-requests';

    public const COUNTRIES = 'countries';

    public const CURRENCIES = 'currencies';

    public const CUSTOMER_GROUPS = 'customer-groups';

    public const CUSTOMERS = 'customers';

    public const EXCHANGE_RATES = 'exchange-rates';

    public const LOCALES = 'locales';

    public const ORDER_ITEM_UNITS = 'order-item-units';

    public const ORDER_ITEMS = 'order-item-units';

    public const ORDERS = 'orders';

    public const PAYMENT_METHODS = 'payment-methods';

    public const PAYMENTS = 'payments';

    public const PRODUCT_ASSOCIATION_TYPES = 'product-association-types';

    public const PRODUCT_OPTIONS = 'product-options';

    public const PRODUCT_TAXONS = 'product-taxons';

    public const PRODUCT_REVIEWS = 'product-reviews';

    public const PRODUCT_VARIANTS = 'product-variants';

    public const PRODUCTS = 'products';

    public const PROMOTIONS = 'promotions';

    public const PROVINCES = 'provinces';

    public const SHIPMENTS = 'shipments';

    public const SHIPPING_CATEGORIES = 'shipping-categories';

    public const SHIPPING_METHODS = 'shipping-methods';

    public const TAX_CATEGORIES = 'tax-categories';

    public const ZONES = 'zones';

    private function __construct()
    {
    }
}
