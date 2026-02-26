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

use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\RequestResetPasswordEmailHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\ResetPasswordHandler as AdminResetPasswordHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\SendResetPasswordEmailHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\ResendOrderConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\ResendShipmentConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account\ResetPasswordHandler as ShopResetPasswordHandler;
use Sylius\Bundle\CoreBundle\CommandHandler\Shop\ChangeShopUserPasswordHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.command_handler.shop.account.change_password', ChangeShopUserPasswordHandler::class)
        ->args([
            service('sylius.security.password_updater'),
            service('sylius.repository.shop_user'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.admin.account.request_reset_password_email', RequestResetPasswordEmailHandler::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.admin_user.token_generator.password_reset'),
            service('clock'),
            service('messenger.default_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.resend_shipment_confirmation_email', ResendShipmentConfirmationEmailHandler::class)
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.mailer.shipment_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.admin.account.reset_password', AdminResetPasswordHandler::class)
        ->args([service('sylius.resetter.user_password.admin')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.shop.account.reset_password', ShopResetPasswordHandler::class)
        ->args([service('sylius.resetter.user_password.shop')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.resend_order_confirmation_email', ResendOrderConfirmationEmailHandler::class)
        ->args([
            service('sylius.mailer.order_email_manager'),
            service('sylius.repository.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;

    $services
        ->set('sylius.command_handler.admin.account.send_reset_password_email', SendResetPasswordEmailHandler::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.mailer.reset_password_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus'])
    ;
};
