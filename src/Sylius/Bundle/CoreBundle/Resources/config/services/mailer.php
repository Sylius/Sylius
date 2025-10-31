<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.user_mailer', 'Sylius\Bundle\CoreBundle\EventListener\MailerListener')
        ->args([
            service('sylius.email_sender'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.password_reset.request.token', 'method' => 'sendResetPasswordTokenEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.email_verification.token', 'method' => 'sendVerificationTokenEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.post_email_verification', 'method' => 'sendVerificationSuccessEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.post_register', 'method' => 'sendUserRegistrationEmail']);

    $services->set('sylius.mailer.contact_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager')
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface', 'sylius.mailer.contact_email_manager');

    $services->set('sylius.mailer.order_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager')
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface', 'sylius.mailer.order_email_manager');

    $services->set('sylius.mailer.shipment_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManager')
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface', 'sylius.mailer.shipment_email_manager');

    $services->set('sylius.mailer.reset_password_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManager')
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface', 'sylius.mailer.reset_password_email_manager');

    $services->set('sylius.mailer.account_registration_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManager')
        ->public()
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface', 'sylius.mailer.account_registration_email_manager')
        ->public();

    $services->set('sylius.mailer.account_verification_email_manager', 'Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManager')
        ->public()
        ->args([service('sylius.email_sender')]);

    $services->alias('Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface', 'sylius.mailer.account_verification_email_manager')
        ->public();
};
