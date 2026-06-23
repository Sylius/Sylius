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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxAutocompleteFilterType;
use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxTranslatableAutocompleteFilterType;
use Sylius\Bundle\AdminBundle\Grid\AdminUserGrid;
use Sylius\Bundle\AdminBundle\Grid\AdminUserGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CatalogPromotionGrid;
use Sylius\Bundle\AdminBundle\Grid\CatalogPromotionGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ChannelGrid;
use Sylius\Bundle\AdminBundle\Grid\ChannelGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ChannelPricingLogEntryGrid;
use Sylius\Bundle\AdminBundle\Grid\ChannelPricingLogEntryGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CountryGrid;
use Sylius\Bundle\AdminBundle\Grid\CountryGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CurrencyGrid;
use Sylius\Bundle\AdminBundle\Grid\CurrencyGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CustomerGrid;
use Sylius\Bundle\AdminBundle\Grid\CustomerGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CustomerGroupGrid;
use Sylius\Bundle\AdminBundle\Grid\CustomerGroupGridInterface;
use Sylius\Bundle\AdminBundle\Grid\CustomerOrderGrid;
use Sylius\Bundle\AdminBundle\Grid\CustomerOrderGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ExchangeRateGrid;
use Sylius\Bundle\AdminBundle\Grid\ExchangeRateGridInterface;
use Sylius\Bundle\AdminBundle\Grid\InventoryGrid;
use Sylius\Bundle\AdminBundle\Grid\InventoryGridInterface;
use Sylius\Bundle\AdminBundle\Grid\LocaleGrid;
use Sylius\Bundle\AdminBundle\Grid\LocaleGridInterface;
use Sylius\Bundle\AdminBundle\Grid\OrderGrid;
use Sylius\Bundle\AdminBundle\Grid\OrderGridInterface;
use Sylius\Bundle\AdminBundle\Grid\PaymentGrid;
use Sylius\Bundle\AdminBundle\Grid\PaymentGridInterface;
use Sylius\Bundle\AdminBundle\Grid\PaymentMethodGrid;
use Sylius\Bundle\AdminBundle\Grid\PaymentMethodGridInterface;
use Sylius\Bundle\AdminBundle\Grid\PaymentRequestGrid;
use Sylius\Bundle\AdminBundle\Grid\PaymentRequestGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductAssociationTypeGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductAssociationTypeGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductAttributeGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductAttributeGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductOptionGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductOptionGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductReviewGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductReviewGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductTaxonGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductTaxonGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductVariantGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductVariantGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ProductVariantWithCatalogPromotionGrid;
use Sylius\Bundle\AdminBundle\Grid\ProductVariantWithCatalogPromotionGridInterface;
use Sylius\Bundle\AdminBundle\Grid\PromotionCouponGrid;
use Sylius\Bundle\AdminBundle\Grid\PromotionCouponGridInterface;
use Sylius\Bundle\AdminBundle\Grid\PromotionGrid;
use Sylius\Bundle\AdminBundle\Grid\PromotionGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ShipmentGrid;
use Sylius\Bundle\AdminBundle\Grid\ShipmentGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ShippingCategoryGrid;
use Sylius\Bundle\AdminBundle\Grid\ShippingCategoryGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ShippingMethodGrid;
use Sylius\Bundle\AdminBundle\Grid\ShippingMethodGridInterface;
use Sylius\Bundle\AdminBundle\Grid\TaxCategoryGrid;
use Sylius\Bundle\AdminBundle\Grid\TaxCategoryGridInterface;
use Sylius\Bundle\AdminBundle\Grid\TaxRateGrid;
use Sylius\Bundle\AdminBundle\Grid\TaxRateGridInterface;
use Sylius\Bundle\AdminBundle\Grid\TaxonGrid;
use Sylius\Bundle\AdminBundle\Grid\TaxonGridInterface;
use Sylius\Bundle\AdminBundle\Grid\ZoneGrid;
use Sylius\Bundle\AdminBundle\Grid\ZoneGridInterface;
use Sylius\Component\Grid\Filter\EntityFilter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.grid_filter.ux_autocomplete', EntityFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'ux_autocomplete', 'form_type' => UxAutocompleteFilterType::class])
    ;

    $services
        ->set('sylius_admin.grid_filter.ux_translatable_autocomplete', EntityFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'ux_translatable_autocomplete', 'form_type' => UxTranslatableAutocompleteFilterType::class])
    ;

    $services->set('sylius_admin.grid.admin_user', AdminUserGrid::class)
        ->args([
            '%sylius.model.admin_user.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(AdminUserGridInterface::class, 'sylius_admin.grid.admin_user');

    $services->set('sylius_admin.grid.catalog_promotion', CatalogPromotionGrid::class)
        ->args([
            '%sylius.model.catalog_promotion.class%',
            '%sylius.model.channel.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CatalogPromotionGridInterface::class, 'sylius_admin.grid.catalog_promotion');

    $services->set('sylius_admin.grid.channel', ChannelGrid::class)
        ->args([
            '%sylius.model.channel.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ChannelGridInterface::class, 'sylius_admin.grid.channel');

    $services->set('sylius_admin.grid.currency', CurrencyGrid::class)
        ->args([
            '%sylius.model.currency.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CurrencyGridInterface::class, 'sylius_admin.grid.currency');

    $services->set('sylius_admin.grid.customer', CustomerGrid::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.model.customer_group.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CustomerGridInterface::class, 'sylius_admin.grid.customer');

    $services->set('sylius_admin.grid.customer_group', CustomerGroupGrid::class)
        ->args([
            '%sylius.model.customer_group.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CustomerGroupGridInterface::class, 'sylius_admin.grid.customer_group');

    $services->set('sylius_admin.grid.locale', LocaleGrid::class)
        ->args([
            '%sylius.model.locale.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(LocaleGridInterface::class, 'sylius_admin.grid.locale');

    $services->set('sylius_admin.grid.inventory', InventoryGrid::class)
        ->args([
            '%sylius.model.product_variant.class%',
            '%sylius.model.product.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(InventoryGridInterface::class, 'sylius_admin.grid.inventory');

    $services->set('sylius_admin.grid.payment_method', PaymentMethodGrid::class)
        ->args([
            '%sylius.model.payment_method.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(PaymentMethodGridInterface::class, 'sylius_admin.grid.payment_method');

    $services->set('sylius_admin.grid.product_association_type', ProductAssociationTypeGrid::class)
        ->args([
            '%sylius.model.product_association_type.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductAssociationTypeGridInterface::class, 'sylius_admin.grid.product_association_type');

    $services->set('syllabus_admin.grid.taxon', TaxonGrid::class)
        ->args([
            '%sylius.model.taxon.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(TaxonGridInterface::class, 'sylius_admin.grid.taxon');

    $services->set('sylius_admin.grid.product_taxon', ProductTaxonGrid::class)
        ->args([
            '%sylius.model.product_taxon.class%',
            '%sylius.model.channel.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductTaxonGridInterface::class, 'sylius_admin.grid.product_taxon');

    $services->set('sylius_admin.grid.product', ProductGrid::class)
        ->args([
            '%sylius.model.product.class%',
            '%sylius.model.channel.class%',
            '%sylius.model.taxon.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductGridInterface::class, 'sylius_admin.grid.product');

    $services->set('sylius_admin.grid.product_attribute', ProductAttributeGrid::class)
        ->args([
            '%sylius.model.product_attribute.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductAttributeGridInterface::class, 'sylius_admin.grid.product_attribute');

    $services->set('sylius_admin.grid.product_variant', ProductVariantGrid::class)
        ->args([
            '%sylius.model.product_variant.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductVariantGridInterface::class, 'sylius_admin.grid.product_variant');

    $services->set('sylius_admin.grid.product_variant_with_catalog_promotion', ProductVariantWithCatalogPromotionGrid::class)
        ->args([
            '%sylius.model.product_variant.class%',
            '%locale%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductVariantWithCatalogPromotionGridInterface::class, 'sylius_admin.grid.product_variant_with_catalog_promotion');

    $services->set('sylius_admin.grid.country', CountryGrid::class)
        ->args([
            '%sylius.model.country.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CountryGridInterface::class, 'sylius_admin.grid.country');

    $services->set('sylius_admin.grid.shipment', ShipmentGrid::class)
        ->args([
            '%sylius.model.shipment.class%',
            '%sylius.model.channel.class%',
            '%sylius.model.shipping_method.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ShipmentGridInterface::class, 'sylius_admin.grid.shipment');

    $services->set('sylius_admin.grid.shipping_category', ShippingCategoryGrid::class)
        ->args([
            '%sylius.model.shipping_category.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ShippingCategoryGridInterface::class, 'sylius_admin.grid.shipping_category');

    $services->set('sylius_admin.grid.shipping_method', ShippingMethodGrid::class)
        ->args([
            '%sylius.model.shipping_method.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ShippingMethodGridInterface::class, 'sylius_admin.grid.shipping_method');

    $services->set('sylius_admin.grid.tax_category', TaxCategoryGrid::class)
        ->args([
            '%sylius.model.tax_category.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(TaxCategoryGridInterface::class, 'sylius_admin.grid.tax_category');

    $services->set('sylius_admin.grid.tax_rate', TaxRateGrid::class)
        ->args([
            '%sylius.model.tax_rate.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(TaxRateGridInterface::class, 'sylius_admin.grid.tax_rate');

    $services->set('sylius_admin.grid.promotion_coupon', PromotionCouponGrid::class)
        ->args([
            '%sylius.model.promotion_coupon.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(PromotionCouponGridInterface::class, 'sylius_admin.grid.promotion_coupon');

    $services->set('sylius_admin.grid.promotion', PromotionGrid::class)
        ->args([
            '%sylius.model.promotion.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(PromotionGridInterface::class, 'sylius_admin.grid.promotion');

    $services->set('sylius_admin.grid.zone', ZoneGrid::class)
        ->args([
            '%sylius.model.zone.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(ZoneGridInterface::class, 'sylius_admin.grid.zone');

    $services->set('sylius_admin.grid.payment', PaymentGrid::class)
        ->args([
            '%sylius.model.payment.class%',
            '%sylius.model.channel.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(PaymentGridInterface::class, 'sylius_admin.grid.payment');

    $services->set('sylius_admin.grid.customer_order', CustomerOrderGrid::class)
        ->args([
            '%sylius.model.order.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(CustomerOrderGridInterface::class, 'sylius_admin.grid.customer_order');

    $services->set('sylius_admin.grid.order', OrderGrid::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.model.channel.class%',
            '%sylius.model.customer.class%',
            '%sylius.model.product.class%',
            '%sylius.model.product_variant.class%',
            '%sylius.model.shipping_method.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(OrderGridInterface::class, 'sylius_admin.grid.order');

    $services->set('sylius_admin.grid.exchange_rate', ExchangeRateGrid::class)
        ->args([
            '%sylius.model.exchange_rate.class%',
            '%sylius.model.currency.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ExchangeRateGridInterface::class, 'sylius_admin.grid.exchange_rate');

    $services->set('sylius_admin.grid.product_option', ProductOptionGrid::class)
        ->args([
            '%sylius.model.product_option.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductOptionGridInterface::class, 'sylius_admin.grid.product_option');

    $services->set('sylius_admin.grid.product_review', ProductReviewGrid::class)
        ->args([
            '%sylius.model.product_review.class%',
            '%sylius.model.product.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ProductReviewGridInterface::class, 'sylius_admin.grid.product_review');

    $services->set('sylius_admin.grid.channel_pricing_log_entry', ChannelPricingLogEntryGrid::class)
        ->args([
            '%sylius.model.channel_pricing_log_entry.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;
    $services->alias(ChannelPricingLogEntryGridInterface::class, 'sylius_admin.grid.channel_pricing_log_entry');

    $services->set('sylius_admin.grid.payment_request', PaymentRequestGrid::class)
        ->args([
            '%sylius.model.payment_request.class%',
            '%sylius.model.payment_method.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(PaymentRequestGridInterface::class, 'sylius_admin.grid.payment_request');
};
