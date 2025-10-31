<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.fixture.listener.catalog_promotion_executor', 'Sylius\Bundle\CoreBundle\Fixture\Listener\CatalogPromotionExecutorListener')
        ->private()
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('sylius.command_bus'),
            tagged_iterator('sylius.catalog_promotion.criteria'),
        ])
        ->tag('sylius_fixtures.listener');

    $services->set('sylius.fixture.listener.images_purger', 'Sylius\Bundle\CoreBundle\Fixture\Listener\ImagesPurgerListener')
        ->private()
        ->args([
            service('filesystem'),
            '%sylius_core.images_dir%',
        ])
        ->tag('sylius_fixtures.listener');
};
