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
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav navbar-nav navbar-right',
            ],
        ]);

        $childOptions = [
            'attributes' => ['class' => 'dropdown'],
            'childrenAttributes' => ['class' => 'dropdown-menu'],
            'labelAttributes' => ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'href' => '#'],
        ];

        $menu->addChild('dashboard', [
            'route' => 'sylius_backend_dashboard',
        ])->setLabel($this->translate('sylius.backend.menu.main.dashboard'));

        $this->addAssortmentMenu($menu, $childOptions, 'main');
        $this->addSalesMenu($menu, $childOptions, 'main');
        $this->addCustomerMenu($menu, $childOptions, 'main');
        $this->addMarketingMenu($menu, $childOptions, 'main');
        $this->addSupportMenu($menu, $childOptions, 'main');
        $this->addContentMenu($menu, $childOptions, 'main');
        $this->addConfigurationMenu($menu, $childOptions, 'main');
        $this->addReviewsMenu($menu, $childOptions, 'main');

        $menu->addChild('homepage', [
            'route' => 'sylius_homepage',
        ])->setLabel($this->translate('sylius.backend.menu.main.homepage'));

        $menu->addChild('logout', [
            'route' => 'sylius_user_security_logout',
        ])->setLabel($this->translate('sylius.backend.logout'));

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
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav',
            ],
        ]);

        $menu->setCurrentUri($this->request->getRequestUri());

        $childOptions = [
            'childrenAttributes' => ['class' => 'nav'],
            'labelAttributes' => ['class' => 'nav-header'],
        ];

        $this->addAssortmentMenu($menu, $childOptions, 'sidebar');
        $this->addSalesMenu($menu, $childOptions, 'sidebar');
        $this->addMarketingMenu($menu, $childOptions, 'sidebar');
        $this->addCustomerMenu($menu, $childOptions, 'sidebar');
        $this->addSupportMenu($menu, $childOptions, 'sidebar');
        $this->addContentMenu($menu, $childOptions, 'sidebar');
        $this->addReviewsMenu($menu, $childOptions, 'sidebar');

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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.taxon.index')) {
            $child->addChild('taxons', [
                'route' => 'sylius_backend_taxon_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-folder-close'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxons', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product.index')) {
            $child->addChild('products', [
                'route' => 'sylius_backend_product_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-th-list'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.products', $section)));
            $child->addChild('inventory', [
                'route' => 'sylius_backend_inventory_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-tasks'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.stockables', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product_option.index')) {
            $child->addChild('options', [
                'route' => 'sylius_backend_product_option_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-th'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.options', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product_attribute.index')) {
            $child->addChild('product_attributes', [
                'route' => 'sylius_backend_product_attribute_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-list-alt'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.attributes', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product_archetype.index')) {
            $child->addChild('product_archetypes', [
                'route' => 'sylius_backend_product_archetype_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-compressed'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.archetypes', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product_association_type.index')) {
            $child->addChild('product_association_types', [
                'route' => 'sylius_backend_product_association_type_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-th-list'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.association_types', $section)));
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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.simple_block.index')) {
            $child->addChild('blocks', [
                'route' => 'sylius_backend_block_overview',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-th-large'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.blocks', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.static_content.index')) {
            $child->addChild('Pages', [
                'route' => 'sylius_backend_static_content_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-file'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.menu.index')) {
            $child->addChild('Menus', [
                'route' => 'sylius_backend_menu_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-list-alt'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.menus', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.slideshow.index')) {
            $child->addChild('Slideshow', [
                'route' => 'sylius_backend_slideshow_block_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-film'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.slideshow', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.route.index')) {
            $child->addChild('Routes', [
                'route' => 'sylius_backend_route_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-random'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.routes', $section)));
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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.promotion.index')) {
            $child->addChild('promotions', [
                'route' => 'sylius_backend_promotion_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-bullhorn'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.promotions', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.promotion.create')) {
            $child->addChild('new_promotion', [
                'route' => 'sylius_backend_promotion_create',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-plus-sign'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.new_promotion', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.manage.email')) {
            $child->addChild('emails', [
                'route' => 'sylius_backend_email_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-envelope'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.emails', $section)));
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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.contact_request.index')) {
            $child->addChild('contact_requests', [
                'route' => 'sylius_backend_contact_request_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-envelope'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.contact_requests', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.contact_topic.index')) {
            $child->addChild('contact_topics', [
                'route' => 'sylius_backend_contact_topic_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-align-justify'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.contact_topics', $section)));
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
    protected function addCustomerMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('customer', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.customer', $section)))
        ;

        if ($this->rbacAuthorizationChecker->isGranted('sylius.customer.index')) {
            $child->addChild('customers', [
                'route' => 'sylius_backend_customer_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-user'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.customers', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.group.index')) {
            $child->addChild('groups', [
                'route' => 'sylius_backend_group_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-home'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.groups', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.role.index')) {
            $child->addChild('roles', [
                'route' => 'sylius_backend_role_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-sort-by-attributes'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.roles', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.permission.index')) {
            $child->addChild('permissions', [
                'route' => 'sylius_backend_permission_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-lock'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.permissions', $section)));
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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.order.index')) {
            $child->addChild('orders', [
                'route' => 'sylius_backend_order_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-shopping-cart'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.orders', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.shipment.index')) {
            $child->addChild('shipments', [
                'route' => 'sylius_backend_shipment_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-plane'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipments', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.payment.index')) {
            $child->addChild('payments', [
                'route' => 'sylius_backend_payment_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-credit-card'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payments', $section)));
        }
        if ($this->rbacAuthorizationChecker->isGranted('sylius.report.index')) {
            $child->addChild('reports', [
                'route' => 'sylius_backend_report_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-stats'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.report', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('sales');
        }
    }

    /**
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    public function addReviewsMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('review', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.review', $section)))
        ;

        if ($this->rbacAuthorizationChecker->isGranted('sylius.product_review.index')) {
            $child->addChild('reviews', [
                'route' => 'sylius_backend_product_review_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-pencil'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.product_review', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('review');
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

        if ($this->rbacAuthorizationChecker->isGranted('sylius.settings.sylius_general')) {
            $child->addChild('general_settings', [
                'route' => 'sylius_backend_general_settings',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-info-sign'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.general_settings', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.settings.sylius_security')) {
            $child->addChild('security_settings', [
                'route' => 'sylius_backend_security_settings',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-lock'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.security_settings', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.channel.index')) {
            $child->addChild('channels', [
                'route' => 'sylius_backend_channel_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.channels', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.metadata_container.index')) {
            $child->addChild('metadata', [
                'route' => 'sylius_backend_metadata_container_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-file'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.metadata', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.locale.index')) {
            $child->addChild('locales', [
                'route' => 'sylius_backend_locale_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-flag'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.locales', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.payment_method.index')) {
            $child->addChild('payment_methods', [
                'route' => 'sylius_backend_payment_method_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-credit-card'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payment_methods', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.currency.index')) {
            $child->addChild('currencies', [
                'route' => 'sylius_backend_currency_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-usd'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.currencies', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.settings.sylius_taxation')) {
            $child->addChild('taxation_settings', [
                'route' => 'sylius_backend_taxation_settings',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxation_settings', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.tax_category.index')) {
            $child->addChild('tax_categories', [
                'route' => 'sylius_backend_tax_category_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_categories', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.tax_rate.index')) {
            $child->addChild('tax_rates', [
                'route' => 'sylius_backend_tax_rate_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_rates', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.shipping_category.index')) {
            $child->addChild('shipping_categories', [
                'route' => 'sylius_backend_shipping_category_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_categories', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.shipping_method.index')) {
            $child->addChild('shipping_methods', [
                'route' => 'sylius_backend_shipping_method_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-cog'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_methods', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.country.index')) {
            $child->addChild('countries', [
                'route' => 'sylius_backend_country_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-flag'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.countries', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.zone.index')) {
            $child->addChild('zones', [
                'route' => 'sylius_backend_zone_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-globe'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.zones', $section)));
        }

        if ($this->rbacAuthorizationChecker->isGranted('sylius.api_client.index')) {
            $child->addChild('api_clients', [
                'route' => 'sylius_backend_api_client_index',
                'labelAttributes' => ['icon' => 'glyphicon glyphicon-globe'],
            ])->setLabel($this->translate(sprintf('sylius.backend.menu.%s.api_clients', $section)));
        }

        if (!$child->hasChildren()) {
            $menu->removeChild('configuration');
        }
    }
}
