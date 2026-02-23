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

use Sylius\Behat\Context\Domain\NotificationContext;
use Sylius\Behat\Context\Domain\ManagingOrdersContext;
use Sylius\Behat\Context\Domain\ManagingPaymentsContext;
use Sylius\Behat\Context\Domain\ManagingPriceHistoryContext;
use Sylius\Behat\Context\Domain\ManagingProductsContext;
use Sylius\Behat\Context\Domain\ManagingPromotionsContext;
use Sylius\Behat\Context\Domain\ManagingPromotionCouponsContext;
use Sylius\Behat\Context\Domain\SecurityContext;
use Sylius\Behat\Context\Domain\ManagingShipmentsContext;
use Sylius\Behat\Context\Domain\CartContext;
use Sylius\Behat\Context\Domain\ManagingShippingMethodsContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services->set('sylius.behat.context.domain.notification', NotificationContext::class);

    $services
        ->set('sylius.behat.context.domain.managing_orders', ManagingOrdersContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.order'),
            service('sylius.repository.order_item'),
            service('sylius.repository.address'),
            service('sylius.repository.adjustment'),
            service('sylius.manager.order'),
            service('sylius.resolver.product_variant'),
            service('sylius.updater.unpaid_orders_state'),
        ])
    ;

    $services
        ->set('sylius.behat.context.domain.managing_payments', ManagingPaymentsContext::class)
        ->args([service('sylius.repository.payment')])
    ;

    $services
        ->set(ManagingPriceHistoryContext::class)
        ->args([
            service('sylius.repository.channel_pricing_log_entry'),
            service('sylius.resolver.product_variant'),
            service('sylius.remover.channel_pricing_log_entries'),
        ])
    ;

    $services
        ->set('sylius.behat.context.domain.managing_products', ManagingProductsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.product'),
            service('sylius.repository.product_variant'),
            service('sylius.repository.product_review'),
        ])
    ;

    $services
        ->set('sylius.behat.context.domain.managing_promotions', ManagingPromotionsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.promotion'),
            service('sylius.manager.promotion'),
        ])
    ;

    $services
        ->set('sylius.behat.context.domain.managing_promotion_coupons', ManagingPromotionCouponsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.promotion_coupon'),
        ])
    ;

    $services->set('sylius.behat.context.domain.security', SecurityContext::class);

    $services
        ->set('sylius.behat.context.domain.managing_shipments', ManagingShipmentsContext::class)
        ->args([service('sylius.repository.shipment')])
    ;

    $services
        ->set('sylius.behat.context.domain.cart', CartContext::class)
        ->args([
            service('sylius.manager.order'),
            service('sylius.remover.expired_carts'),
        ])
    ;

    $services
        ->set('sylius.behat.context.domain.managing_shipping_methods', ManagingShippingMethodsContext::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.manager.shipping_method'),
        ])
    ;
};
