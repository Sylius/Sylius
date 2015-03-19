<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;

/**
 * Main menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackendMenuBuilder extends MenuBuilder
{
    /**
     * Builds backend main menu.
     *
     * @return ItemInterface
     */
    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right'
            )
        ));

        $childOptions = array(
            'attributes'         => array('class' => 'dropdown'),
            'childrenAttributes' => array('class' => 'dropdown-menu'),
            'labelAttributes'    => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'href' => '#')
        );

        $menu->addChild('dashboard', array(
            'route' => 'sylius_backend_dashboard'
        ))->setLabel($this->translate('sylius.backend.menu.main.dashboard'));

        $this->addAssortmentMenu($menu, $childOptions, 'main');
        $this->addSalesMenu($menu, $childOptions, 'main');
        $this->addCustomersMenu($menu, $childOptions, 'main');
        $this->addMarketingMenu($menu, $childOptions, 'main');
        $this->addSupportMenu($menu, $childOptions, 'main');
        $this->addContentMenu($menu, $childOptions, 'main');
        $this->addConfigurationMenu($menu, $childOptions, 'main');

        $menu->addChild('homepage', array(
            'route' => 'sylius_homepage'
        ))->setLabel($this->translate('sylius.backend.menu.main.homepage'));

        $menu->addChild('logout', array(
            'route' => 'fos_user_security_logout'
        ))->setLabel($this->translate('sylius.backend.logout'));

        $this->eventDispatcher->dispatch(MenuBuilderEvent::BACKEND_MAIN, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds backend sidebar menu.
     *
     * @return ItemInterface
     */
    public function createSidebarMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $menu->setCurrentUri($this->request->getRequestUri());

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav'),
            'labelAttributes'    => array('class' => 'nav-header')
        );

        $this->addAssortmentMenu($menu, $childOptions, 'sidebar');
        $this->addSalesMenu($menu, $childOptions, 'sidebar');
        $this->addMarketingMenu($menu, $childOptions, 'sidebar');
        $this->addCustomersMenu($menu, $childOptions, 'sidebar');
        $this->addSupportMenu($menu, $childOptions, 'sidebar');
        $this->addContentMenu($menu, $childOptions, 'sidebar');
        $this->addConfigurationMenu($menu, $childOptions, 'sidebar');

        $this->eventDispatcher->dispatch(MenuBuilderEvent::BACKEND_SIDEBAR, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Add assortment menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('assortment', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.assortment', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.taxonomy.index')) {
            $child->addChild('taxonomies', array(
                'route' => 'sylius_backend_taxonomy_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxonomies', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.product.index')) {
            $child->addChild('products', array(
                'route' => 'sylius_backend_product_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.products', $section)));
            $child->addChild('inventory', array(
                'route' => 'sylius_backend_inventory_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-tasks'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.stockables', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.product_option.index')) {
            $child->addChild('options', array(
                'route' => 'sylius_backend_product_option_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-th'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.options', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.product_attribute.index')) {
            $child->addChild('product_attributes', array(
                'route' => 'sylius_backend_product_attribute_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-list-alt'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.attributes', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.product_archetype.index')) {
            $child->addChild('product_archetypes', array(
                'route' => 'sylius_backend_product_archetype_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.archetypes', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('assortment');
        }
    }

    /**
     * Add content menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addContentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('content', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.content', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.simple_block.index')) {
            $child->addChild('blocks', array(
                'route' => 'sylius_backend_block_overview',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-large'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.blocks', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.static_content.index')) {
            $child->addChild('Pages', array(
                'route' => 'sylius_backend_static_content_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-file'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.menu.index')) {
            $child->addChild('Menus', array(
                'route' => 'sylius_backend_menu_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-list-alt'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.menus', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.slideshow.index')) {
            $child->addChild('Slideshow', array(
                'route' => 'sylius_backend_slideshow_block_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-film'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.slideshow', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.route.index')) {
            $child->addChild('Routes', array(
                'route' => 'sylius_backend_route_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-random'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.routes', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('content');
        }
    }

    /**
     * Add marketing menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addMarketingMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('marketing', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.marketing', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.promotion.index')) {
            $child->addChild('promotions', array(
                'route' => 'sylius_backend_promotion_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-bullhorn'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.promotions', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.promotion.create')) {
            $child->addChild('new_promotion', array(
                'route' => 'sylius_backend_promotion_create',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-plus-sign'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.new_promotion', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.promotion.index')) {
            $child->addChild('emails', array(
                'route' => 'sylius_backend_email_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-envelope'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.emails', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('marketing');
        }
    }

    /**
     * Add support menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addSupportMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('support', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.support', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.contact_request.index')) {
            $child->addChild('contact_requests', array(
                'route' => 'sylius_backend_contact_request_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-envelope'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.contact_requests', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.contact_topic.index')) {
            $child->addChild('contact_topics', array(
                'route' => 'sylius_backend_contact_topic_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-align-justify'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.contact_topics', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('support');
        }
    }

    /**
     * Add customers menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addCustomersMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('customer', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.customer', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.user.index')) {
            $child->addChild('users', array(
                'route' => 'sylius_backend_user_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-user'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.users', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.group.index')) {
            $child->addChild('groups', array(
                'route' => 'sylius_backend_group_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-home'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.groups', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.role.index')) {
            $child->addChild('roles', array(
                'route' => 'sylius_backend_role_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-sort-by-attributes'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.roles', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.permission.index')) {
            $child->addChild('permissions', array(
                'route' => 'sylius_backend_permission_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-lock'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.permissions', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('customer');
        }
    }

    /**
     * Add sales menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addSalesMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('sales', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.sales', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.order.index')) {
            $child->addChild('orders', array(
                'route' => 'sylius_backend_order_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.orders', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.shipment.index')) {
            $child->addChild('shipments', array(
                'route' => 'sylius_backend_shipment_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-plane'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipments', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.payment.index')) {
            $child->addChild('payments', array(
                'route' => 'sylius_backend_payment_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-credit-card'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payments', $section)));
        }
        if ($this->authorizationChecker->isGranted('sylius.report.index')) {
            $child->addChild('reports', array(
                'route' => 'sylius_backend_report_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-stats'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.report', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('sales');
        }
    }

    /**
     * Add configuration menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addConfigurationMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('configuration', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.configuration', $section)))
        ;

        if ($this->authorizationChecker->isGranted('sylius.settings.general')) {
            $child->addChild('general_settings', array(
                'route'           => 'sylius_backend_general_settings',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-info-sign'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.general_settings', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.settings.security')) {
            $child->addChild('security_settings', array(
                'route'           => 'sylius_backend_security_settings',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-lock'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.security_settings', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.locale.index')) {
            $child->addChild('locales', array(
                'route' => 'sylius_backend_locale_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-flag'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.locales', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.payment_method.index')) {
            $child->addChild('payment_methods', array(
                'route' => 'sylius_backend_payment_method_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-credit-card'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payment_methods', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.currency.index')) {
            $child->addChild('currencies', array(
                'route' => 'sylius_backend_currency_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-usd'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.currencies', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.settings.taxation')) {
            $child->addChild('taxation_settings', array(
                'route'           => 'sylius_backend_taxation_settings',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxation_settings', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.tax_category.index')) {
            $child->addChild('tax_categories', array(
                'route' => 'sylius_backend_tax_category_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_categories', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.tax_rate.index')) {
            $child->addChild('tax_rates', array(
                'route' => 'sylius_backend_tax_rate_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_rates', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.shipping_category.index')) {
            $child->addChild('shipping_categories', array(
                'route' => 'sylius_backend_shipping_category_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_categories', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.shipping_method.index')) {
            $child->addChild('shipping_methods', array(
                'route' => 'sylius_backend_shipping_method_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_methods', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.country.index')) {
            $child->addChild('countries', array(
                'route' => 'sylius_backend_country_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-flag'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.countries', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.zone.index')) {
            $child->addChild('zones', array(
                'route' => 'sylius_backend_zone_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-globe'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.zones', $section)));
        }

        if ($this->authorizationChecker->isGranted('sylius.api_client.index')) {
            $child->addChild('api_clients', array(
                'route' => 'sylius_backend_api_client_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-globe'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.api_clients', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('configuration');
        }
    }
}
