<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.command_handler.catalog_promotion.apply_variants', 'Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\ApplyCatalogPromotionsOnVariantsHandler')
        ->args([
            service('sylius.provider.eligible_catalog_promotions'),
            service('sylius.applicator.catalog_promotion'),
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.clearer'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.disable', 'Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\DisableCatalogPromotionHandler')
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.processor.catalog_promotion.all_product_variant'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.remove', 'Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveCatalogPromotionHandler')
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.manager.catalog_promotion'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.update_state', 'Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\UpdateCatalogPromotionStateHandler')
        ->args([
            service('sylius.processor.catalog_promotion.state'),
            service('sylius.repository.catalog_promotion'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
