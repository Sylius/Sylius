<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.command_dispatcher.resend_order_confirmation_email', 'Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcher')
        ->args([service('sylius.command_bus')]);

    $services->alias('Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcherInterface', 'sylius.command_dispatcher.resend_order_confirmation_email');

    $services->set('sylius.command_dispatcher.resend_shipment_confirmation_email', 'Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcher')
        ->args([service('sylius.command_bus')]);

    $services->alias('Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcherInterface', 'sylius.command_dispatcher.resend_shipment_confirmation_email');

    $services->set('sylius.command_dispatcher.reset_password', 'Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcher')
        ->args([service('sylius.command_bus')]);

    $services->alias('Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcherInterface', 'sylius.command_dispatcher.reset_password');
};
