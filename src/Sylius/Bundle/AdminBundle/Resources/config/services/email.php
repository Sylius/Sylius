<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.mailer.shipment_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManager')
        ->args([service('sylius.email_sender')]);
};
