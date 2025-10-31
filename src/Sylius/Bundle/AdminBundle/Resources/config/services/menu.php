<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.menu_builder.main', 'Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder')
        ->args([
            service('knp_menu.factory'),
            service('event_dispatcher'),
            service('router'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => 'sylius_admin.main']);
};
