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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MainMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.main';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->addCatalogSubMenu($menu);
        $this->addSalesSubMenu($menu);
        $this->addCustomersSubMenu($menu);
        $this->addMarketingSubMenu($menu);
        $this->addConfigurationSubMenu($menu);

        $this->eventDispatcher->dispatch(self::EVENT_NAME, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    private function addCatalogSubMenu(ItemInterface $menu): void
    {
        $catalog = $menu
            ->addChild('catalog')
            ->setLabel('sylius.menu.admin.main.catalog.header')
        ;

        $catalog
            ->addChild('taxons', ['route' => 'sylius_admin_taxon_create'])
            ->setLabel('sylius.menu.admin.main.catalog.taxons')
            ->setLabelAttribute('icon', 'folder')
        ;

        $catalog
            ->addChild('products', ['route' => 'sylius_admin_product_index'])
            ->setLabel('sylius.menu.admin.main.catalog.products')
            ->setLabelAttribute('icon', 'cube')
        ;

        $catalog
            ->addChild('inventory', ['route' => 'sylius_admin_inventory_index'])
            ->setLabel('sylius.menu.admin.main.catalog.inventory')
            ->setLabelAttribute('icon', 'history')
        ;

        $catalog
            ->addChild('attributes', ['route' => 'sylius_admin_product_attribute_index'])
            ->setLabel('sylius.menu.admin.main.catalog.attributes')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $catalog
            ->addChild('options', ['route' => 'sylius_admin_product_option_index'])
            ->setLabel('sylius.menu.admin.main.catalog.options')
            ->setLabelAttribute('icon', 'options')
        ;

        $catalog
            ->addChild('association_types', ['route' => 'sylius_admin_product_association_type_index'])
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
            ->addChild('customers', ['route' => 'sylius_admin_customer_index'])
            ->setLabel('sylius.menu.admin.main.customers.customers')
            ->setLabelAttribute('icon', 'users')
        ;

        $customers
            ->addChild('groups', ['route' => 'sylius_admin_customer_group_index'])
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
            ->addChild('promotions', ['route' => 'sylius_admin_promotion_index'])
            ->setLabel('sylius.menu.admin.main.marketing.promotions')
            ->setLabelAttribute('icon', 'in cart')
        ;

        $marketing
            ->addChild('product_reviews', ['route' => 'sylius_admin_product_review_index'])
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
            ->addChild('orders', ['route' => 'sylius_admin_order_index'])
            ->setLabel('sylius.menu.admin.main.sales.orders')
            ->setLabelAttribute('icon', 'cart')
        ;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.menu.admin.main.configuration.header')
        ;

        $configuration
            ->addChild('channels', ['route' => 'sylius_admin_channel_index'])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'random')
        ;

        $configuration
            ->addChild('countries', ['route' => 'sylius_admin_country_index'])
            ->setLabel('sylius.menu.admin.main.configuration.countries')
            ->setLabelAttribute('icon', 'flag')
        ;

        $configuration
            ->addChild('zones', ['route' => 'sylius_admin_zone_index'])
            ->setLabel('sylius.menu.admin.main.configuration.zones')
            ->setLabelAttribute('icon', 'world')
        ;

        $configuration
            ->addChild('currencies', ['route' => 'sylius_admin_currency_index'])
            ->setLabel('sylius.menu.admin.main.configuration.currencies')
            ->setLabelAttribute('icon', 'dollar')
        ;

        $configuration
            ->addChild('exchange_rates', ['route' => 'sylius_admin_exchange_rate_index'])
            ->setLabel('sylius.menu.admin.main.configuration.exchange_rates')
            ->setLabelAttribute('icon', 'sliders')
        ;

        $configuration
            ->addChild('locales', ['route' => 'sylius_admin_locale_index'])
            ->setLabel('sylius.menu.admin.main.configuration.locales')
            ->setLabelAttribute('icon', 'translate')
        ;

        $configuration
            ->addChild('payment_methods', ['route' => 'sylius_admin_payment_method_index'])
            ->setLabel('sylius.menu.admin.main.configuration.payment_methods')
            ->setLabelAttribute('icon', 'payment')
        ;

        $configuration
            ->addChild('shipping_methods', ['route' => 'sylius_admin_shipping_method_index'])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_methods')
            ->setLabelAttribute('icon', 'shipping')
        ;

        $configuration
            ->addChild('shipping_categories', ['route' => 'sylius_admin_shipping_category_index'])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_categories')
            ->setLabelAttribute('icon', 'list layout')
        ;

        $configuration
            ->addChild('tax_categories', ['route' => 'sylius_admin_tax_category_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_categories')
            ->setLabelAttribute('icon', 'tags')
        ;

        $configuration
            ->addChild('tax_rates', ['route' => 'sylius_admin_tax_rate_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_rates')
            ->setLabelAttribute('icon', 'money')
        ;

        $configuration
            ->addChild('admin_users', ['route' => 'sylius_admin_admin_user_index'])
            ->setLabel('sylius.menu.admin.main.configuration.admin_users')
            ->setLabelAttribute('icon', 'lock')
        ;
    }
}
