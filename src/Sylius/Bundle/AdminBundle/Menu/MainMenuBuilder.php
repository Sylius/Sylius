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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MainMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.main';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->addCatalogSubMenu($menu);
        $this->addSalesSubMenu($menu);
        $this->addCustomersSubMenu($menu);
        $this->addMarketingSubMenu($menu);
        $this->addConfigurationSubMenu($menu);
        $this->addOfficialSupportSubMenu($menu);

        $this->eventDispatcher->dispatch(new MenuBuilderEvent($this->factory, $menu), self::EVENT_NAME);

        return $menu;
    }

    private function addCatalogSubMenu(ItemInterface $menu): void
    {
        $catalog = $menu
            ->addChild('catalog')
            ->setLabel('sylius.menu.admin.main.catalog.header')
        ;

        $catalog
            ->addChild('taxons', ['route' => 'sylius_admin_taxon_create', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_taxon_create_for_parent'],
                ['route' => 'sylius_admin_taxon_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.taxons')
            ->setLabelAttribute('icon', 'folder')
        ;

        $catalog
            ->addChild('products', ['route' => 'sylius_admin_product_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_create'],
                ['route' => 'sylius_admin_product_create_simple'],
                ['route' => 'sylius_admin_product_update'],
                ['route' => 'sylius_admin_product_show'],
                ['route' => 'sylius_admin_product_variant_index'],
                ['route' => 'sylius_admin_product_variant_create'],
                ['route' => 'sylius_admin_product_variant_update'],
                ['route' => 'sylius_admin_product_variant_generate'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.products')
            ->setLabelAttribute('icon', 'cube')
        ;

        $catalog
            ->addChild('inventory', ['route' => 'sylius_admin_inventory_index'])
            ->setLabel('sylius.menu.admin.main.catalog.inventory')
            ->setLabelAttribute('icon', 'history')
        ;

        $catalog
            ->addChild('attributes', ['route' => 'sylius_admin_product_attribute_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_attribute_create'],
                ['route' => 'sylius_admin_product_attribute_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.attributes')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $catalog
            ->addChild('options', ['route' => 'sylius_admin_product_option_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_option_create'],
                ['route' => 'sylius_admin_product_option_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.options')
            ->setLabelAttribute('icon', 'options')
        ;

        $catalog
            ->addChild('association_types', ['route' => 'sylius_admin_product_association_type_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_association_type_create'],
                ['route' => 'sylius_admin_product_association_type_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.catalog.association_types')
            ->setLabelAttribute('icon', 'tasks')
        ;
    }

    private function addCustomersSubMenu(ItemInterface $menu): void
    {
        $customers = $menu
            ->addChild('customers')
            ->setLabel('sylius.menu.admin.main.customers.header')
        ;

        $customers
            ->addChild('customers', ['route' => 'sylius_admin_customer_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_customer_create'],
                ['route' => 'sylius_admin_customer_update'],
                ['route' => 'sylius_admin_customer_show'],
            ]]])
            ->setLabel('sylius.menu.admin.main.customers.customers')
            ->setLabelAttribute('icon', 'users')
        ;

        $customers
            ->addChild('groups', ['route' => 'sylius_admin_customer_group_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_customer_group_create'],
                ['route' => 'sylius_admin_customer_group_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.customers.groups')
            ->setLabelAttribute('icon', 'archive')
        ;
    }

    private function addMarketingSubMenu(ItemInterface $menu): void
    {
        $marketing = $menu
            ->addChild('marketing')
            ->setLabel('sylius.menu.admin.main.marketing.header')
        ;

        $marketing
            ->addChild('promotions', ['route' => 'sylius_admin_promotion_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_promotion_create'],
                ['route' => 'sylius_admin_promotion_update'],
                ['route' => 'sylius_admin_promotion_coupon_index'],
                ['route' => 'sylius_admin_promotion_coupon_create'],
                ['route' => 'sylius_admin_promotion_coupon_update'],
                ['route' => 'sylius_admin_promotion_coupon_generate'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.cart_promotions')
            ->setLabelAttribute('icon', 'in cart')
        ;

        $marketing
            ->addChild('catalog_promotions', ['route' => 'sylius_admin_catalog_promotion_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_catalog_promotion_create'],
                ['route' => 'sylius_admin_catalog_promotion_update'],
                ['route' => 'sylius_admin_catalog_promotion_show'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.catalog_promotions')
            ->setLabelAttribute('icon', 'bookmark')
        ;

        $marketing
            ->addChild('product_reviews', ['route' => 'sylius_admin_product_review_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_product_review_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.marketing.product_reviews')
            ->setLabelAttribute('icon', 'newspaper')
        ;
    }

    private function addSalesSubMenu(ItemInterface $menu): void
    {
        $sales = $menu
            ->addChild('sales')
            ->setLabel('sylius.menu.admin.main.sales.header')
        ;

        $sales
            ->addChild('orders', ['route' => 'sylius_admin_order_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_order_update'],
                ['route' => 'sylius_admin_order_show'],
                ['route' => 'sylius_admin_order_history'],
            ]]])
            ->setLabel('sylius.menu.admin.main.sales.orders')
            ->setLabelAttribute('icon', 'cart')
        ;

        $sales
            ->addChild('payments', ['route' => 'sylius_admin_payment_index'])
            ->setLabel('sylius.ui.payments')
            ->setLabelAttribute('icon', 'payment')
        ;

        $sales
            ->addChild('shipments', ['route' => 'sylius_admin_shipment_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipment_show'],
            ]]])
            ->setLabel('sylius.ui.shipments')
            ->setLabelAttribute('icon', 'truck')
        ;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.menu.admin.main.configuration.header')
        ;

        $configuration
            ->addChild('channels', ['route' => 'sylius_admin_channel_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_channel_create'],
                ['route' => 'sylius_admin_channel_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'random')
        ;

        $configuration
            ->addChild('countries', ['route' => 'sylius_admin_country_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_country_create'],
                ['route' => 'sylius_admin_country_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.countries')
            ->setLabelAttribute('icon', 'flag')
        ;

        $configuration
            ->addChild('zones', ['route' => 'sylius_admin_zone_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_zone_create'],
                ['route' => 'sylius_admin_zone_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.zones')
            ->setLabelAttribute('icon', 'world')
        ;

        $configuration
            ->addChild('currencies', ['route' => 'sylius_admin_currency_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_currency_create'],
                ['route' => 'sylius_admin_currency_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.currencies')
            ->setLabelAttribute('icon', 'dollar')
        ;

        $configuration
            ->addChild('exchange_rates', ['route' => 'sylius_admin_exchange_rate_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_exchange_rate_create'],
                ['route' => 'sylius_admin_exchange_rate_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.exchange_rates')
            ->setLabelAttribute('icon', 'sliders')
        ;

        $configuration
            ->addChild('locales', ['route' => 'sylius_admin_locale_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_locale_create'],
                ['route' => 'sylius_admin_locale_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.locales')
            ->setLabelAttribute('icon', 'translate')
        ;

        $configuration
            ->addChild('payment_methods', ['route' => 'sylius_admin_payment_method_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_payment_method_create'],
                ['route' => 'sylius_admin_payment_method_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.payment_methods')
            ->setLabelAttribute('icon', 'payment')
        ;

        $configuration
            ->addChild('shipping_methods', ['route' => 'sylius_admin_shipping_method_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipping_method_create'],
                ['route' => 'sylius_admin_shipping_method_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_methods')
            ->setLabelAttribute('icon', 'shipping')
        ;

        $configuration
            ->addChild('shipping_categories', ['route' => 'sylius_admin_shipping_category_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipping_category_create'],
                ['route' => 'sylius_admin_shipping_category_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_categories')
            ->setLabelAttribute('icon', 'list layout')
        ;

        $configuration
            ->addChild('tax_categories', ['route' => 'sylius_admin_tax_category_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_tax_category_create'],
                ['route' => 'sylius_admin_tax_category_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.tax_categories')
            ->setLabelAttribute('icon', 'tags')
        ;

        $configuration
            ->addChild('tax_rates', ['route' => 'sylius_admin_tax_rate_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_tax_rate_create'],
                ['route' => 'sylius_admin_tax_rate_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.tax_rates')
            ->setLabelAttribute('icon', 'money')
        ;

        $configuration
            ->addChild('admin_users', ['route' => 'sylius_admin_admin_user_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_admin_user_create'],
                ['route' => 'sylius_admin_admin_user_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.configuration.admin_users')
            ->setLabelAttribute('icon', 'lock')
        ;
    }

    private function addOfficialSupportSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('official_support')
            ->setLabel('sylius.menu.admin.main.official_support.header')
        ;

        $configuration
            ->addChild('sylius_plus')
            ->setUri('https://sylius.com/plus/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.sylius_plus')
            ->setLabelAttribute('icon', 'plus')
        ;

        $configuration
            ->addChild('browse_plugins')
            ->setUri('https://store.sylius.com/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.browse_plugins')
            ->setLabelAttribute('icon', 'plug')
        ;

        $configuration
            ->addChild('professional_services')
            ->setUri('https://sylius.com/services/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.professional_services')
            ->setLabelAttribute('icon', 'cog')
        ;

        $configuration
            ->addChild('find_a_partner')
            ->setUri('https://sylius.com/find-a-partner/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.find_a_partner')
            ->setLabelAttribute('icon', 'handshake')
        ;

        $configuration
            ->addChild('sylius_certification')
            ->setUri('https://sylius.com/certification/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.sylius_certification')
            ->setLabelAttribute('icon', 'certificate')
        ;
    }
}
