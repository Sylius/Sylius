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

use Sylius\Behat\Context\Transform\AddressContext;
use Sylius\Behat\Context\Transform\AdminUserContext;
use Sylius\Behat\Context\Transform\CartContext;
use Sylius\Behat\Context\Transform\CatalogPromotionContext;
use Sylius\Behat\Context\Transform\ChannelContext;
use Sylius\Behat\Context\Transform\CountryContext;
use Sylius\Behat\Context\Transform\CouponContext;
use Sylius\Behat\Context\Transform\CurrencyContext;
use Sylius\Behat\Context\Transform\CustomerContext;
use Sylius\Behat\Context\Transform\CustomerGroupContext;
use Sylius\Behat\Context\Transform\DateTimeContext;
use Sylius\Behat\Context\Transform\ExchangeRateContext;
use Sylius\Behat\Context\Transform\LexicalContext;
use Sylius\Behat\Context\Transform\LocaleContext;
use Sylius\Behat\Context\Transform\OrderContext;
use Sylius\Behat\Context\Transform\PaymentMethodContext;
use Sylius\Behat\Context\Transform\ProductAssociationTypeContext;
use Sylius\Behat\Context\Transform\ProductAttributeContext;
use Sylius\Behat\Context\Transform\ProductContext;
use Sylius\Behat\Context\Transform\ProductOptionContext;
use Sylius\Behat\Context\Transform\ProductOptionValueContext;
use Sylius\Behat\Context\Transform\ProductReviewContext;
use Sylius\Behat\Context\Transform\ProductVariantContext;
use Sylius\Behat\Context\Transform\PromotionContext;
use Sylius\Behat\Context\Transform\ProvinceContext;
use Sylius\Behat\Context\Transform\SharedStorageContext;
use Sylius\Behat\Context\Transform\ShippingCalculatorContext;
use Sylius\Behat\Context\Transform\ShippingCategoryContext;
use Sylius\Behat\Context\Transform\ShippingMethodContext;
use Sylius\Behat\Context\Transform\ShopUserContext;
use Sylius\Behat\Context\Transform\TaxCategoryContext;
use Sylius\Behat\Context\Transform\TaxonContext;
use Sylius\Behat\Context\Transform\TaxRateContext;
use Sylius\Behat\Context\Transform\ThemeContext;
use Sylius\Behat\Context\Transform\UserContext;
use Sylius\Behat\Context\Transform\ZoneContext;
use Sylius\Behat\Context\Transform\ZoneMemberContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.transform.address', AddressContext::class)
        ->args([
            service('sylius.factory.address'),
            service('sylius.converter.country_name'),
            service('sylius.repository.address'),
            service('sylius.fixture.example_factory.address'),
        ])
    ;

    $services
        ->set(CatalogPromotionContext::class)
        ->args([service('sylius.repository.catalog_promotion')])
    ;

    $services
        ->set('sylius.behat.context.transform.channel', ChannelContext::class)
        ->args([service('sylius.repository.channel')])
    ;

    $services
        ->set('sylius.behat.context.transform.country', CountryContext::class)
        ->args([
            service('sylius.converter.country_name'),
            service('sylius.repository.country'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.coupon', CouponContext::class)
        ->args([service('sylius.repository.promotion_coupon')])
    ;

    $services
        ->set('sylius.behat.context.transform.currency', CurrencyContext::class)
        ->args([
            service('sylius.converter.currency_name'),
            service('sylius.repository.currency'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.customer', CustomerContext::class)
        ->args([
            service('sylius.repository.customer'),
            service('sylius.factory.customer'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.customer_group', CustomerGroupContext::class)
        ->args([service('sylius.repository.customer_group')])
    ;

    $services->set('sylius.behat.context.transform.date_time', DateTimeContext::class);

    $services
        ->set('sylius.behat.context.transform.exchange_rate', ExchangeRateContext::class)
        ->args([
            service('sylius.converter.currency_name'),
            service('sylius.repository.currency'),
            service('sylius.repository.exchange_rate'),
        ])
    ;

    $services->set('sylius.behat.context.transform.lexical', LexicalContext::class);

    $services
        ->set('sylius.behat.context.transform.locale', LocaleContext::class)
        ->args([
            service('sylius.converter.locale'),
            service('sylius.repository.locale'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.order', OrderContext::class)
        ->args([
            service('sylius.repository.customer'),
            service('sylius.repository.order'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.payment', PaymentMethodContext::class)
        ->args([service('sylius.repository.payment_method')])
    ;

    $services
        ->set('sylius.behat.context.transform.product', ProductContext::class)
        ->args([
            service('sylius.repository.product'),
            '%locale%',
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.product_association_type', ProductAssociationTypeContext::class)
        ->args([service('sylius.repository.product_association_type')])
    ;

    $services
        ->set('sylius.behat.context.transform.product_attribute', ProductAttributeContext::class)
        ->args([service('sylius.repository.product_attribute_translation')])
    ;

    $services
        ->set('sylius.behat.context.transform.product_option', ProductOptionContext::class)
        ->args([service('sylius.repository.product_option')])
    ;

    $services
        ->set('sylius.behat.context.transform.product_option_value', ProductOptionValueContext::class)
        ->args([service('sylius.repository.product_option_value')])
    ;

    $services
        ->set('sylius.behat.context.transform.product_review', ProductReviewContext::class)
        ->args([service('sylius.repository.product_review')])
    ;

    $services
        ->set('sylius.behat.context.transform.product_variant', ProductVariantContext::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.repository.product_variant'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.promotion', PromotionContext::class)
        ->args([
            service('sylius.repository.promotion'),
            service('sylius.repository.promotion_coupon'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.province', ProvinceContext::class)
        ->args([service('sylius.repository.province')])
    ;

    $services
        ->set('sylius.behat.context.transform.shared_storage', SharedStorageContext::class)
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.context.transform.shipping_calculator', ShippingCalculatorContext::class)
        ->args([
            '%sylius.shipping_calculators%',
            service('translator'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.shipping_category', ShippingCategoryContext::class)
        ->args([service('sylius.repository.shipping_category')])
    ;

    $services
        ->set('sylius.behat.context.transform.shipping_method', ShippingMethodContext::class)
        ->args([service('sylius.repository.shipping_method')])
    ;

    $services
        ->set('sylius.behat.context.transform.tax_category', TaxCategoryContext::class)
        ->args([service('sylius.repository.tax_category')])
    ;

    $services
        ->set('sylius.behat.context.transform.tax_rate', TaxRateContext::class)
        ->args([service('sylius.repository.tax_rate')])
    ;

    $services
        ->set('sylius.behat.context.transform.taxon', TaxonContext::class)
        ->args([
            service('sylius.repository.taxon'),
            '%locale%',
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.theme', ThemeContext::class)
        ->args([service('sylius.repository.theme')])
    ;

    $services
        ->set('sylius.behat.context.transform.user', UserContext::class)
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.context.transform.admin', AdminUserContext::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.cart', CartContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.zone', ZoneContext::class)
        ->args([service('sylius.repository.zone')])
    ;

    $services
        ->set('sylius.behat.context.transform.zone_member', ZoneMemberContext::class)
        ->args([
            service('sylius.converter.country_name'),
            service('sylius.repository.province'),
            service('sylius.repository.zone'),
            service('sylius.repository.zone_member'),
        ])
    ;

    $services
        ->set('sylius.behat.context.transform.shop_user', ShopUserContext::class)
        ->args([service('sylius.repository.shop_user')])
    ;
};
