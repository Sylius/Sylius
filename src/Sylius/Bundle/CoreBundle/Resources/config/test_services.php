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

use Sylius\Component\Core\Test\Services\DefaultChannelFactory;
use Sylius\Component\Core\Test\Services\DefaultUnitedStatesChannelFactory;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.behat.factory.default_united_states_channel', DefaultUnitedStatesChannelFactory::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.repository.country'),
            service('sylius.repository.currency'),
            service('sylius.repository.locale'),
            service('sylius.repository.zone'),
            service('sylius.factory.channel'),
            service('sylius.factory.country'),
            service('sylius.factory.currency'),
            service('sylius.factory.locale'),
            service('sylius.factory.zone'),
            '%locale%',
        ]);

    $services->set('sylius.behat.factory.default_channel', DefaultChannelFactory::class)
        ->args([
            service('sylius.factory.channel'),
            service('sylius.factory.currency'),
            service('sylius.factory.locale'),
            service('sylius.factory.shop_billing_data'),
            service('sylius.repository.channel'),
            service('sylius.repository.currency'),
            service('sylius.repository.locale'),
            '%locale%',
        ]);

    $services->alias('sylius.liip.filter_service', 'liip_imagine.service.filter');
};
