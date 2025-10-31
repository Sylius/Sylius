<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.command_handler.admin.account.request_reset_password_email', 'Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\RequestResetPasswordEmailHandler')
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.admin_user.token_generator.password_reset'),
            service('clock'),
            service('messenger.default_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.resend_shipment_confirmation_email', 'Sylius\Bundle\CoreBundle\CommandHandler\ResendShipmentConfirmationEmailHandler')
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.mailer.shipment_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.admin.account.reset_password', 'Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\ResetPasswordHandler')
        ->args([service('sylius.resetter.user_password.admin')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.resend_order_confirmation_email', 'Sylius\Bundle\CoreBundle\CommandHandler\ResendOrderConfirmationEmailHandler')
        ->args([
            service('sylius.mailer.order_email_manager'),
            service('sylius.repository.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.admin.account.send_reset_password_email', 'Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\SendResetPasswordEmailHandler')
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.mailer.reset_password_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
