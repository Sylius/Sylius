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

use Sylius\Bundle\UiBundle\Controller\SecurityController;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.controller.security', SecurityController::class)
        ->args([
            service('security.authentication_utils'),
            service('form.factory'),
            service('twig'),
            service('security.authorization_checker'),
            service('router'),
        ]);
};
