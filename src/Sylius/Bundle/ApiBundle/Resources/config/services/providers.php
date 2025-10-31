<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.api_path_prefixes', [\Sylius\Bundle\ApiBundle\Provider\PathPrefixes::ADMIN_PREFIX, \Sylius\Bundle\ApiBundle\Provider\PathPrefixes::SHOP_PREFIX]);

    $services->set('sylius_api.provider.path_prefix', 'Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider')
        ->args([
            '%sylius.security.api_route%',
            '%sylius.api_path_prefixes%',
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface', 'sylius_api.provider.path_prefix');

    $services->set('sylius_api.provider.liip_image_filters', 'Sylius\Bundle\ApiBundle\Provider\LiipImageFiltersProvider')
        ->args(['%liip_imagine.filter_sets%']);

    $services->alias('Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface', 'sylius_api.provider.liip_image_filters');

    $services->set('sylius_api.provider.adjustment_order', 'Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProvider');

    $services->alias('Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface', 'sylius_api.provider.adjustment_order');
};
