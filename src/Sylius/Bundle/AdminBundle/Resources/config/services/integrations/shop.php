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

use Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController;
use Sylius\Bundle\CoreBundle\Security\UserImpersonator;
use Sylius\Bundle\CoreBundle\Security\UserImpersonatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.security.shop_user_impersonator', UserImpersonator::class)
        ->args([
            service('request_stack'),
            '%sylius_shop.firewall_context_name%',
            service('event_dispatcher'),
        ]);

    $services->alias(UserImpersonatorInterface::class, 'sylius_admin.security.shop_user_impersonator');

    $services->set('sylius_admin.controller.impersonate_user', ImpersonateUserController::class)
        ->public()
        ->args([
            service('sylius_admin.security.shop_user_impersonator'),
            service('security.authorization_checker'),
            service('sylius.shop_user_provider.email_or_name_based'),
            service('router'),
            'ROLE_ADMINISTRATION_ACCESS',
        ]);
};
