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

        $this->addConfigurationMenu($menu);

        $this->eventDispatcher->dispatch(self::EVENT_NAME, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function addConfigurationMenu(ItemInterface $menu)
    {
        $this->configureMarketingSubMenu($menu);
        $this->configureCustomersSubMenu($menu);
        $this->configureCatalogSubMenu($menu);
        $this->configureConfigurationSubMenu($menu);
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
    private function configureCustomersSubMenu(ItemInterface $menu)
    {
        $customersSubMenu = $menu
            ->addChild('customers')
            ->setLabel('sylius.menu.admin.main.customers.header')
        ;

        $customersSubMenu
            ->addChild('customers', ['route' => 'sylius_admin_customer_index'])
            ->setLabel('sylius.menu.admin.main.customers.customers')
            ->setLabelAttribute('icon', 'users')
        ;
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
            ->addChild('attributes', ['route' => 'sylius_admin_product_attribute_index'])
            ->setLabel('sylius.menu.admin.main.catalog.attributes')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $catalogSubMenu
            ->addChild('product_options', ['route' => 'sylius_admin_product_option_index'])
            ->setLabel('sylius.menu.admin.main.catalog.product_options')
            ->setLabelAttribute('icon', 'options')
        ;

        $catalogSubMenu
            ->addChild('taxons', ['route' => 'sylius_admin_taxon_create'])
            ->setLabel('sylius.menu.admin.main.catalog.taxons')
            ->setLabelAttribute('icon', 'folder')
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
            ->addChild('tax_categories', ['route' => 'sylius_admin_tax_category_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_categories')
            ->setLabelAttribute('icon', 'tags')
        ;

        $configurationSubMenu
            ->addChild('countries', ['route' => 'sylius_admin_country_index'])
            ->setLabel('sylius.menu.admin.main.configuration.countries')
            ->setLabelAttribute('icon', 'flag')
        ;

        $configurationSubMenu
            ->addChild('locale', ['route' => 'sylius_admin_locale_index'])
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
            ->addChild('tax_rates', ['route' => 'sylius_admin_tax_rate_index'])
            ->setLabel('sylius.menu.admin.main.configuration.tax_rates')
            ->setLabelAttribute('icon', 'dollar')
        ;

        $configurationSubMenu
            ->addChild('zones', ['route' => 'sylius_admin_zone_index'])
            ->setLabel('sylius.menu.admin.main.configuration.zones')
            ->setLabelAttribute('icon', 'world')
        ;

        $configurationSubMenu
            ->addChild('currencies', ['route' => 'sylius_admin_currency_index'])
            ->setLabel('sylius.menu.admin.main.configuration.currencies')
            ->setLabelAttribute('icon', 'money')
        ;

        $configurationSubMenu
            ->addChild('channels', ['route' => 'sylius_admin_channel_index'])
            ->setLabel('sylius.menu.admin.main.configuration.channels')
            ->setLabelAttribute('icon', 'random')
        ;
    }
}
