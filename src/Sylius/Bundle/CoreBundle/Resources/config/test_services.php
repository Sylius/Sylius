<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.behat.factory.default_united_states_channel', 'Sylius\Component\Core\Test\Services\DefaultUnitedStatesChannelFactory')
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

    $services->set('sylius.behat.factory.default_channel', 'Sylius\Component\Core\Test\Services\DefaultChannelFactory')
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
