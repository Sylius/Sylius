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

use Sylius\Component\Core\Factory\PaymentMethodFactory;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.custom_factory.payment_method', PaymentMethodFactory::class)
        ->decorate('sylius.factory.payment_method')
        ->args([
            service('sylius.custom_factory.payment_method.inner'),
            service('sylius.factory.gateway_config'),
        ])
    ;
    $services->alias(PaymentMethodFactoryInterface::class, 'sylius.custom_factory.payment_method');
};
