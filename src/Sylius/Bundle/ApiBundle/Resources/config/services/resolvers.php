<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.operation_resolver.path_prefix_based', 'Sylius\Bundle\ApiBundle\Resolver\PathPrefixBasedOperationResolver')
        ->args([service('api_platform.metadata.resource.metadata_collection_factory')]);

    $services->alias('Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface', 'sylius_api.operation_resolver.path_prefix_based');

    $services->set('sylius_api.resolver.uri_template_parent_resource_resolver', 'Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolver')
        ->args([service('doctrine.orm.entity_manager')]);
};
