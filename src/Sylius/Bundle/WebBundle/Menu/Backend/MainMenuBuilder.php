<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Backend;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Event\MenuEvent;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MainMenuBuilder extends MenuBuilder
{
    const NAME = 'sylius.backend.main';

    /**
     * Builds backend main menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu
            ->addChild('dashboard', array('route' => 'sylius_backend_dashboard'))
            ->setLabel('sylius.backend.main.dashboard')
        ;

        $this->addCatalogMenu($menu);
        $this->addSalesMenu($menu);
        $this->addMarketingMenu($menu);
        $this->addCustomersMenu($menu);
        $this->addContentMenu($menu);
        $this->addConfigurationMenu($menu);

        $this->eventDispatcher->dispatch(self::NAME, new MenuEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Add catalog menu.
     *
     * @param ItemInterface $menu
     */
    protected function addCatalogMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('catalog')
            ->setLabel('sylius.backend.main.catalog')
        ;

        $child
            ->addChild('categorization', array('route' => 'sylius_backend_taxonomy_index'))
            ->setLabel('sylius.backend.main.taxonomies')
        ;
        $child
            ->addChild('products', array('route' => 'sylius_backend_product_index'))
            ->setLabel('sylius.backend.main.products')
        ;
        $child
            ->addChild('inventory', array('route' => 'sylius_backend_inventory_index'))
            ->setLabel('sylius.backend.main.stockables')
        ;
        $child
            ->addChild('options', array('route' => 'sylius_backend_product_option_index'))
            ->setLabel('sylius.backend.main.options')
        ;
        $child
            ->addChild('product_attributes', array('route' => 'sylius_backend_product_attribute_index'))
            ->setLabel('sylius.backend.main.attributes')
        ;
        $child
            ->addChild('prototypes', array('route' => 'sylius_backend_product_prototype_index'))
            ->setLabel('sylius.backend.main.prototypes')
        ;
    }

    /**
     * Add content menu.
     *
     * @param ItemInterface $menu
     */
    protected function addContentMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('content')
            ->setLabel('sylius.backend.main.content')
        ;

        $child
            ->addChild('blocks', array('route' => 'sylius_backend_block_index'))
            ->setLabel('sylius.backend.main.blocks')
        ;
        $child
            ->addChild('Pages', array('route' => 'sylius_backend_page_index'))
            ->setLabel('sylius.backend.main.pages')
        ;
    }

    /**
     * Add customers menu.
     *
     * @param ItemInterface $menu
     */
    protected function addCustomersMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('customer')
            ->setLabel('sylius.backend.main.customer')
        ;

        $child
            ->addChild('users', array('route' => 'sylius_backend_user_index'))
            ->setLabel('sylius.backend.main.users')
        ;
        $child
            ->addChild('groups', array('route' => 'sylius_backend_group_index'))
            ->setLabel('sylius.backend.main.groups')
        ;
    }

    /**
     * Add sales menu.
     *
     * @param ItemInterface $menu
     */
    protected function addSalesMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('sales')
            ->setLabel('sylius.backend.main.sales')
        ;

        $child
            ->addChild('orders', array('route' => 'sylius_backend_order_index'))
            ->setLabel('sylius.backend.main.orders')
        ;
        $child
            ->addChild('shipments', array('route' => 'sylius_backend_shipment_index'))
            ->setLabel('sylius.backend.main.shipments')
        ;
        $child
            ->addChild('new_order', array('route' => 'sylius_backend_order_create'))
            ->setLabel('sylius.backend.main.new_order')
        ;
        $child
            ->addChild('payments', array('route' => 'sylius_backend_payment_index'))
            ->setLabel('sylius.backend.main.payments')
        ;
    }

    /**
     * Add marketing menu.
     *
     * @param ItemInterface $menu
     */
    protected function addMarketingMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('marketing')
            ->setLabel('sylius.backend.main.marketing')
        ;

        $child
            ->addChild('promotions', array('route' => 'sylius_backend_promotion_index'))
            ->setLabel('sylius.backend.main.promotions')
        ;
        $child
            ->addChild('new_promotion', array('route' => 'sylius_backend_promotion_create'))
            ->setLabel('sylius.backend.main.new_promotion')
        ;
    }

    /**
     * Add configuration menu.
     *
     * @param ItemInterface $menu
     */
    protected function addConfigurationMenu(ItemInterface $menu)
    {
        $child = $menu
            ->addChild('configuration')
            ->setLabel('sylius.backend.main.configuration')
        ;

        $child
            ->addChild('general_settings', array('route' => 'sylius_backend_general_settings'))
            ->setLabel('sylius.backend.main.general_settings')
        ;
        $child
            ->addChild('locales', array('route' => 'sylius_backend_locale_index'))
            ->setLabel('sylius.backend.main.locales')
        ;
        $child
            ->addChild('payment_methods', array('route' => 'sylius_backend_payment_method_index'))
            ->setLabel('sylius.backend.main.payment_methods')
        ;
        $child
            ->addChild('exchange_rates', array('route' => 'sylius_backend_exchange_rate_index'))
            ->setLabel('sylius.backend.main.exchange_rates')
        ;
        $child
            ->addChild('taxation_settings', array('route' => 'sylius_backend_taxation_settings'))
            ->setLabel('sylius.backend.main.taxation_settings')
        ;
        $child
            ->addChild('tax_categories', array('route' => 'sylius_backend_tax_category_index'))
            ->setLabel('sylius.backend.main.tax_categories')
        ;
        $child
            ->addChild('tax_rates', array('route' => 'sylius_backend_tax_rate_index'))
            ->setLabel('sylius.backend.main.tax_rates')
        ;
        $child
            ->addChild('shipping_categories', array('route' => 'sylius_backend_shipping_category_index'))
            ->setLabel('sylius.backend.main.shipping_categories')
        ;
        $child
            ->addChild('shipping_methods', array('route' => 'sylius_backend_shipping_method_index'))
            ->setLabel('sylius.backend.main.shipping_methods')
        ;
        $child
            ->addChild('countries', array('route' => 'sylius_backend_country_index'))
            ->setLabel('sylius.backend.main.countries')
        ;
        $child
            ->addChild('zones', array('route' => 'sylius_backend_zone_index'))
            ->setLabel('sylius.backend.main.zones')
        ;
    }
}
