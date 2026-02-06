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

use Sylius\Bundle\CoreBundle\Provider\DeliveryTimeProviderInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Common\DeliveryTimeComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.twig.component.common.delivery_time', DeliveryTimeComponent::class)
        ->args([
            service(DeliveryTimeProviderInterface::class),
            service('sylius.context.channel'),
            service('translator'),
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:common:delivery_time'])
    ;
};
