<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.oauth.user_provider', 'Sylius\Bundle\CoreBundle\OAuth\UserProvider')
        ->lazy()
        ->args([
            '',
            service('sylius.factory.customer'),
            service('sylius.factory.shop_user'),
            service('sylius.repository.shop_user'),
            service('sylius.factory.oauth_user'),
            service('sylius.repository.oauth_user'),
            service('sylius.manager.shop_user'),
            service('sylius.canonicalizer'),
            service('sylius.repository.customer'),
        ]);
};
