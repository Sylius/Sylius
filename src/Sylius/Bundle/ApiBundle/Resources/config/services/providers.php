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

use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProvider;
use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface;
use Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface;
use Sylius\Bundle\ApiBundle\Provider\LiipImageFiltersProvider;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixes;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.api_path_prefixes', [PathPrefixes::ADMIN_PREFIX, PathPrefixes::SHOP_PREFIX]);

    $services->set('sylius_api.provider.path_prefix', PathPrefixProvider::class)
        ->args([
            '%sylius.security.api_route%',
            '%sylius.api_path_prefixes%',
        ]);

    $services->alias(PathPrefixProviderInterface::class, 'sylius_api.provider.path_prefix');

    $services->set('sylius_api.provider.liip_image_filters', LiipImageFiltersProvider::class)
        ->args(['%liip_imagine.filter_sets%']);

    $services->alias(ImageFiltersProviderInterface::class, 'sylius_api.provider.liip_image_filters');

    $services->set('sylius_api.provider.adjustment_order', AdjustmentOrderProvider::class);

    $services->alias(AdjustmentOrderProviderInterface::class, 'sylius_api.provider.adjustment_order');
};
