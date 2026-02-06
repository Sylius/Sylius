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

use Sylius\Bundle\CoreBundle\EventListener\MailerListener;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.listener.user_mailer', MailerListener::class)
        ->args([
            service('sylius.email_sender'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.password_reset.request.token', 'method' => 'sendResetPasswordTokenEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.email_verification.token', 'method' => 'sendVerificationTokenEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.post_email_verification', 'method' => 'sendVerificationSuccessEmail'])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.post_register', 'method' => 'sendUserRegistrationEmail']);

    $services->set('sylius.mailer.contact_email_manager', ContactEmailManager::class)
        ->args([service('sylius.email_sender')]);

    $services->alias(ContactEmailManagerInterface::class, 'sylius.mailer.contact_email_manager');

    $services->set('sylius.mailer.order_email_manager', OrderEmailManager::class)
        ->args([service('sylius.email_sender')]);

    $services->alias(OrderEmailManagerInterface::class, 'sylius.mailer.order_email_manager');

    $services->set('sylius.mailer.shipment_email_manager', ShipmentEmailManager::class)
        ->args([service('sylius.email_sender')]);

    $services->alias(ShipmentEmailManagerInterface::class, 'sylius.mailer.shipment_email_manager');

    $services->set('sylius.mailer.reset_password_email_manager', ResetPasswordEmailManager::class)
        ->args([service('sylius.email_sender')]);

    $services->alias(ResetPasswordEmailManagerInterface::class, 'sylius.mailer.reset_password_email_manager');

    $services->set('sylius.mailer.account_registration_email_manager', AccountRegistrationEmailManager::class)
        ->public()
        ->args([service('sylius.email_sender')]);

    $services->alias(AccountRegistrationEmailManagerInterface::class, 'sylius.mailer.account_registration_email_manager')
        ->public();

    $services->set('sylius.mailer.account_verification_email_manager', AccountVerificationEmailManager::class)
        ->public()
        ->args([service('sylius.email_sender')]);

    $services->alias(AccountVerificationEmailManagerInterface::class, 'sylius.mailer.account_verification_email_manager')
        ->public();
};
