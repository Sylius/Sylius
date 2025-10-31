<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.shipment.ship_form', 'Sylius\Bundle\AdminBundle\Twig\Component\Shipment\ShipFormComponent')
        ->args([
            service('sylius.repository.shipment'),
            service('form.factory'),
            '%sylius.model.shipment.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ShipmentShipType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:shipment:ship_form']);
};
