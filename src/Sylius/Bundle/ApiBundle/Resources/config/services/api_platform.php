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

use Sylius\Bundle\ApiBundle\ApiPlatform\Hydra\Serializer\EmptyCollectionFiltersNormalizer;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\DuplicateOperationReplacerResourceMetadataCollectionFactory;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\ImageFilterAwareResourceMetadataCollectionFactory;
use Sylius\Bundle\ApiBundle\ApiPlatform\Routing\ApiLoader;
use Sylius\Bundle\ApiBundle\ApiPlatform\Routing\IriConverter;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius_api.normalization.image_filter.supported_interfaces', [ImageInterface::class, ImageAwareInterface::class, ImagesAwareInterface::class]);

    $services
        ->set('sylius_api.api_platform.routing.iri_converter', IriConverter::class)
        ->decorate('api_platform.symfony.iri_converter', null, 64)
        ->args([
            service('.inner'),
            service('sylius_api.provider.path_prefix'),
            service('sylius_api.operation_resolver.path_prefix_based'),
            service('api_platform.router'),
            service('api_platform.resource_class_resolver'),
        ])
    ;

    $services
        ->set('sylius_api.api_platform.routing.api_loader', ApiLoader::class)
        ->decorate('api_platform.route_loader')
        ->args([
            service('.inner'),
            '%sylius_api.operations_to_remove%',
        ])
    ;

    $services
        ->set('sylius_api.api_platform.metadata.resource.metadata_collection_factory.duplicate_operation_replacer', DuplicateOperationReplacerResourceMetadataCollectionFactory::class)
        ->decorate('api_platform.metadata.resource.metadata_collection_factory', null, 799)
        ->args([service('.inner')])
        ->private()
    ;

    $services
        ->set('sylius_api.api_platform.metadata.resource.metadata_collection_factory.image_filter_aware', ImageFilterAwareResourceMetadataCollectionFactory::class)
        ->decorate('api_platform.metadata.resource.metadata_collection_factory')
        ->args([
            service('.inner'),
            '%sylius_api.normalization.image_filter.supported_interfaces%',
        ])
        ->private()
    ;

    $services
        ->set('sylius_api.api_platform.hydra.normalizer.empty_collection_filters', EmptyCollectionFiltersNormalizer::class)
        ->decorate('api_platform.hydra.normalizer.collection')
        ->args([service('.inner')])
        ->private()
    ;
};
