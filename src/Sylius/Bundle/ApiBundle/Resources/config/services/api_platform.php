<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_api.normalization.image_filter.supported_interfaces', ['Sylius\Component\Core\Model\ImageInterface', 'Sylius\Component\Core\Model\ImageAwareInterface', 'Sylius\Component\Core\Model\ImagesAwareInterface']);

    $services->set('sylius_api.api_platform.routing.iri_converter', 'Sylius\Bundle\ApiBundle\ApiPlatform\Routing\IriConverter')
        ->decorate('api_platform.symfony.iri_converter', null, 64)
        ->args([
            service('.inner'),
            service('sylius_api.provider.path_prefix'),
            service('sylius_api.operation_resolver.path_prefix_based'),
            service('api_platform.router'),
        ]);

    $services->set('sylius_api.api_platform.routing.api_loader', 'Sylius\Bundle\ApiBundle\ApiPlatform\Routing\ApiLoader')
        ->decorate('api_platform.route_loader')
        ->args([
            service('.inner'),
            '%sylius_api.operations_to_remove%',
        ]);

    $services->set('sylius_api.api_platform.metadata.resource.metadata_collection_factory.duplicate_operation_replacer', 'Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\DuplicateOperationReplacerResourceMetadataCollectionFactory')
        ->private()
        ->decorate('api_platform.metadata.resource.metadata_collection_factory', null, 799)
        ->args([service('.inner')]);

    $services->set('sylius_api.api_platform.metadata.resource.metadata_collection_factory.image_filter_aware', 'Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\ImageFilterAwareResourceMetadataCollectionFactory')
        ->private()
        ->decorate('api_platform.metadata.resource.metadata_collection_factory')
        ->args([
            service('.inner'),
            '%sylius_api.normalization.image_filter.supported_interfaces%',
        ]);

    $services->set('sylius_api.api_platform.hydra.normalizer.empty_collection_filters', 'Sylius\Bundle\ApiBundle\ApiPlatform\Hydra\Serializer\EmptyCollectionFiltersNormalizer')
        ->private()
        ->decorate('api_platform.hydra.normalizer.collection')
        ->args([service('.inner')]);
};
