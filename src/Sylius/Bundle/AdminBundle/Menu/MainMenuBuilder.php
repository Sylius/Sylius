<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class MainMenuBuilder extends AbstractAdminMenuBuilder
{
    const EVENT_NAME = 'sylius.menu.admin.main';

    /**
     * @return ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        $this->configureCatalogSubMenu($menu);
        $this->configureSalesMenu($menu);
        $this->configureCustomersSubMenu($menu);
        $this->configureMarketingSubMenu($menu);
        $this->configureConfigurationSubMenu($menu);

        $this->eventDispatcher->dispatch(self::EVENT_NAME, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function configureCatalogSubMenu(ItemInterface $menu)
    {
        $catalogSubMenu = $menu
            ->addChild('catalog')
            ->setLabel('sylius.menu.admin.main.catalog.header')
        ;

        $catalogSubMenu
            ->addChild('taxons', ['route' => 'sylius_admin_taxon_create'])
            ->setLabel('sylius.menu.admin.main.catalog.taxons')
            ->setLabelAttribute('icon', 'folder')
        ;

        $catalogSubMenu
            ->addChild('products', ['route' => 'sylius_admin_product_index'])
            ->setLabel('sylius.menu.admin.main.catalog.products')
            ->setLabelAttribute('icon', 'cube')
        ;

        $catalogSubMenu
            ->addChild('attributes', ['route' => 'sylius_admin_product_attribute_index'])
            ->setLabel('sylius.menu.admin.main.catalog.attributes')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $catalogSubMenu
            ->addChild('options', ['route' => 'sylius_admin_product_option_index'])
            ->setLabel('sylius.menu.admin.main.catalog.options')
            ->setLabelAttribute('icon', 'options')
        ;
    }

    /**
     * @param ItemInterface $menu
     */
    private function configureCustomersSubMenu(ItemInterface $menu)
    {
        $customerSubMenu = $menu
            ->addChild('customers')
            ->setLabel('sylius.menu.admin.main.customers.header')
        ;

        $customerSubMenu
            ->addChild('customers', ['route' => 'sylius_admin_customer_index'])
            ->setLabel('sylius.menu.admin.main.customers.customers')
            ->setLabelAttribute('icon', 'users')
        ;
    }

    /**
     * @param ItemInterface $menu
     */
    private function configureMarketingSubMenu(ItemInterface $menu)
    {
        $marketingSubMenu = $menu
            ->addChild('marketing')
            ->setLabel('sylius.menu.admin.main.marketing.header')
        ;

        $marketingSubMenu
            ->addChild('promotions', ['route' => 'sylius_admin_promotion_index'])
            ->setLabel('sylius.menu.admin.main.marketing.promotions')
            ->setLabelAttribute('icon', 'in cart')
        ;
    }

    /**
     * @param ItemInterface $menu
     */
    private function configureSalesMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('sales')
            ->setLabel('sylius.menu.admin.main.sales.header')
        ;

        $child
            ->addChild('orders', ['route' => 'sylius_admin_order_index'])
            ->setLabel('sylius.menu.admin.main.sales.orders')
            ->setLabelAttribute('icon', 'cart')
        ;
    }

    /**
     * @param ItemInterface $menu
     */
    private function configureConfigurationSubMenu(ItemInterface $menu)
    {
        $configurationSubMenu = $menu
            ->addChild('configuration')
            ->setLabel('sylius.menu.admin.main.configuration.header')
        ;

        $configurationSubMenu
            ->addChild('channels', ['route' => 'sylius_admin_channel_index'])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'share alternate')
        ;

        $configurationSubMenu
            ->addChild('countries', ['route' => 'sylius_admin_country_index'])
            ->setLabel('sylius.menu.admin.main.configuration.countries')
            ->setLabelAttribute('icon', 'flag')
        ;

        $configurationSubMenu
            ->addChild('zones', ['route' => 'sylius_admin_zone_index'])
            ->setLabel('sylius.menu.admin.main.configuration.zones')
            ->setLabelAttribute('icon', 'world')
        ;

        $configurationSubMenu
            ->addChild('currencies', ['route' => 'sylius_admin_currency_index'])
            ->setLabel('sylius.menu.admin.main.configuration.currencies')
            ->setLabelAttribute('icon', 'dollar')
        ;

        $configurationSubMenu
            ->addChild('locales', ['route' => 'sylius_admin_locale_index'])
            ->setLabel('sylius.menu.admin.main.configuration.locales')
            ->setLabelAttribute('icon', 'translate')
        ;

        $configurationSubMenu
            ->addChild('payment_methods', ['route' => 'sylius_admin_payment_method_index'])
            ->setLabel('sylius.menu.admin.main.configuration.payment_methods')
            ->setLabelAttribute('icon', 'payment')
        ;

        $configurationSubMenu
            ->addChild('shipping_methods', ['route' => 'sylius_admin_shipping_method_index'])
            ->setLabel('sylius.menu.admin.main.configuration.shipping_methods')
            ->setLabelAttribute('icon', 'shipping')
        ;

        $configurationSubMenu
            ->addChild('tax_categories', ['route' => 'sylius_admin_tax_category_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_categories')
            ->setLabelAttribute('icon', 'tags')
        ;

        $configurationSubMenu
            ->addChild('tax_rates', ['route' => 'sylius_admin_tax_rate_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_rates')
            ->setLabelAttribute('icon', 'money')
        ;

        $configurationSubMenu
            ->addChild('channels', ['route' => 'sylius_admin_channel_index'])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'random')
        ;
    }
}
