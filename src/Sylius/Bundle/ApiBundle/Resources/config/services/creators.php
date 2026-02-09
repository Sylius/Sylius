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

use Sylius\Bundle\ApiBundle\Creator\AvatarImageCreator;
use Sylius\Bundle\ApiBundle\Creator\ProductImageCreator;
use Sylius\Bundle\ApiBundle\Creator\TaxonImageCreator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.creator.avatar_image', AvatarImageCreator::class)
        ->args([
            service('sylius.factory.avatar_image'),
            service('sylius.repository.admin_user'),
            service('sylius.uploader.image'),
            service('api_platform.symfony.iri_converter'),
        ])
    ;

    $services
        ->set('sylius_api.creator.product_image', ProductImageCreator::class)
        ->args([
            service('sylius.factory.product_image'),
            service('sylius.repository.product'),
            service('sylius.uploader.image'),
            service('api_platform.symfony.iri_converter'),
        ])
    ;

    $services
        ->set('sylius_api.creator.taxon_image', TaxonImageCreator::class)
        ->args([
            service('sylius.factory.taxon_image'),
            service('sylius.repository.taxon'),
            service('sylius.uploader.image'),
        ])
    ;
};
