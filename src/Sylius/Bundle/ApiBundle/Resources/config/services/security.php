<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->tag('security.voter');

    $services->set('sylius_api.security.voter.shop_user', 'Sylius\Bundle\ApiBundle\Security\ShopUserVoter');

    $services->set('sylius_api.security.voter.order_adjustments', 'Sylius\Bundle\ApiBundle\Security\OrderAdjustmentsVoter')
        ->args([service('sylius_api.provider.adjustment_order')])
        ->tag('security.voter');
};
