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

use Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.menu_builder.main', MainMenuBuilder::class)
        ->args([
            service('knp_menu.factory'),
            service('event_dispatcher'),
            service('router'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => 'sylius_admin.main']);
};
