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

use Sylius\Bundle\ApiBundle\Controller\DeleteOrderItemAction;
use Sylius\Bundle\ApiBundle\Controller\GetCustomerStatisticsAction;
use Sylius\Bundle\ApiBundle\Controller\GetProductBySlugAction;
use Sylius\Bundle\ApiBundle\Controller\GetStatisticsAction;
use Sylius\Bundle\ApiBundle\Controller\GetTaxonBySlugAction;
use Sylius\Bundle\ApiBundle\Controller\RemoveCatalogPromotionAction;
use Sylius\Bundle\ApiBundle\Controller\RemoveCustomerShopUserAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_api.controller.delete_order_item', DeleteOrderItemAction::class)
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.order_item'),
        ])
    ;

    $services
        ->set('sylius_api.controller.get_customer_statistics', GetCustomerStatisticsAction::class)
        ->args([service('sylius.query_bus')])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_api.controller.get_product_by_slug', GetProductBySlugAction::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('sylius.repository.product'),
            service('api_platform.symfony.iri_converter'),
            service('request_stack'),
        ])
    ;

    $services
        ->set('sylius_api.controller.remove_catalog_promotion', RemoveCatalogPromotionAction::class)
        ->args([service('sylius.processor.catalog_promotion.removal')])
    ;

    $services
        ->set('sylius_api.controller.remove_customer_shop_user', RemoveCustomerShopUserAction::class)
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.shop_user'),
        ])
    ;

    $services
        ->set('sylius_api.controller.get_statistics', GetStatisticsAction::class)
        ->args([
            service('sylius.query_bus'),
            service('serializer'),
            service('validator'),
            '%sylius_core.orders_statistics.intervals_map%',
        ])
    ;

    $services
        ->set('sylius_api.controller.get_taxon_by_slug', GetTaxonBySlugAction::class)
        ->args([
            service('sylius.context.locale'),
            service('sylius.repository.taxon'),
            service('api_platform.symfony.iri_converter'),
            service('request_stack'),
        ])
    ;
};
