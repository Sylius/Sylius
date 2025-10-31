<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.security.shop_user_impersonator', 'Sylius\Bundle\CoreBundle\Security\UserImpersonator')
        ->args([
            service('request_stack'),
            '%sylius_shop.firewall_context_name%',
            service('event_dispatcher'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Security\UserImpersonatorInterface', 'sylius_admin.security.shop_user_impersonator');

    $services->set('sylius_admin.controller.impersonate_user', 'Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController')
        ->public()
        ->args([
            service('sylius_admin.security.shop_user_impersonator'),
            service('security.authorization_checker'),
            service('sylius.shop_user_provider.email_or_name_based'),
            service('router'),
            'ROLE_ADMINISTRATION_ACCESS',
        ]);
};
