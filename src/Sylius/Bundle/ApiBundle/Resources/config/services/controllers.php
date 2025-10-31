<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.controller.delete_order_item', 'Sylius\Bundle\ApiBundle\Controller\DeleteOrderItemAction')
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.order_item'),
        ]);

    $services->set('sylius_api.controller.get_customer_statistics', 'Sylius\Bundle\ApiBundle\Controller\GetCustomerStatisticsAction')
        ->args([service('sylius.query_bus')])
        ->tag('controller.service_arguments');

    $services->set('sylius_api.controller.get_product_by_slug', 'Sylius\Bundle\ApiBundle\Controller\GetProductBySlugAction')
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('sylius.repository.product'),
            service('api_platform.symfony.iri_converter'),
            service('request_stack'),
        ]);

    $services->set('sylius_api.controller.remove_catalog_promotion', 'Sylius\Bundle\ApiBundle\Controller\RemoveCatalogPromotionAction')
        ->args([service('sylius.processor.catalog_promotion.removal')]);

    $services->set('sylius_api.controller.remove_customer_shop_user', 'Sylius\Bundle\ApiBundle\Controller\RemoveCustomerShopUserAction')
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.shop_user'),
        ]);

    $services->set('sylius_api.controller.get_statistics', 'Sylius\Bundle\ApiBundle\Controller\GetStatisticsAction')
        ->args([
            service('sylius.query_bus'),
            service('serializer'),
            service('validator'),
            '%sylius_core.orders_statistics.intervals_map%',
        ]);

    $services->set('sylius_api.controller.get_taxon_by_slug', 'Sylius\Bundle\ApiBundle\Controller\GetTaxonBySlugAction')
        ->args([
            service('sylius.context.locale'),
            service('sylius.repository.taxon'),
            service('api_platform.symfony.iri_converter'),
            service('request_stack'),
        ]);
};
