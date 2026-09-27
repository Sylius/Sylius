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

use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Bundle\ApiBundle\Resolver\PathPrefixBasedOperationResolver;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolver;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.operation_resolver.path_prefix_based', PathPrefixBasedOperationResolver::class)
        ->args([service('api_platform.metadata.resource.metadata_collection_factory')])
    ;
    $services->alias(OperationResolverInterface::class, 'sylius_api.operation_resolver.path_prefix_based');

    $services
        ->set('sylius_api.resolver.uri_template_parent_resource_resolver', UriTemplateParentResourceResolver::class)
        ->args([service('doctrine.orm.entity_manager')])
    ;
};
