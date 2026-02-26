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

use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcher;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcher;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcher;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcherInterface;
use Sylius\Bundle\CoreBundle\CommandDispatcher\Shop\ResetPasswordDispatcher as ShopResetPasswordDispatcher;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.command_dispatcher.resend_order_confirmation_email', ResendOrderConfirmationEmailDispatcher::class)
        ->args([service('sylius.command_bus')])
    ;
    $services->alias(ResendOrderConfirmationEmailDispatcherInterface::class, 'sylius.command_dispatcher.resend_order_confirmation_email');

    $services
        ->set('sylius.command_dispatcher.resend_shipment_confirmation_email', ResendShipmentConfirmationEmailDispatcher::class)
        ->args([service('sylius.command_bus')])
    ;
    $services->alias(ResendShipmentConfirmationEmailDispatcherInterface::class, 'sylius.command_dispatcher.resend_shipment_confirmation_email');

    $services
        ->set('sylius.command_dispatcher.reset_password', ResetPasswordDispatcher::class)
        ->args([service('sylius.command_bus')])
    ;
    $services->alias(ResetPasswordDispatcherInterface::class, 'sylius.command_dispatcher.reset_password');

    $services
        ->set('sylius.command_dispatcher.reset_password.shop', ShopResetPasswordDispatcher::class)
        ->args([service('sylius.command_bus')])
    ;
};
