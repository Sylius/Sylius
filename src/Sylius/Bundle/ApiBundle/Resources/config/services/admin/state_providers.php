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

use Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.state_provider.admin.order.adjustment.collection', CollectionProvider::class)
        ->args([
            service('sylius.repository.order'),
            'tokenValue',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.admin.order_item.adjustment.collection', CollectionProvider::class)
        ->args([
            service('sylius.repository.order_item'),
            'id',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;
};
