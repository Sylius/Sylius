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

use Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProvider;
use Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.provider.payment_configuration', CompositePaymentConfigurationProvider::class)
        ->args([tagged_iterator('sylius.api.payment_method_handler')])
    ;
    $services->alias('sylius_api.provider.payment_configuration.composite', 'sylius_api.provider.payment_configuration');

    $services->alias(CompositePaymentConfigurationProviderInterface::class, 'sylius_api.provider.payment_configuration');
};
