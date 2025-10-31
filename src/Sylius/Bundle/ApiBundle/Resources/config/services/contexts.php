<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.context.user.token_based', 'Sylius\Bundle\ApiBundle\Context\TokenBasedUserContext')
        ->args([service('security.token_storage')]);

    $services->alias('Sylius\Bundle\ApiBundle\Context\UserContextInterface', 'sylius_api.context.user.token_based');

    $services->set('sylius_api.context.cart.token_value_based', 'Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext')
        ->args([
            service('request_stack'),
            service('sylius.repository.order'),
            '%sylius.security.api_route%',
        ])
        ->tag('sylius.context.cart', ['priority' => -333]);
};
