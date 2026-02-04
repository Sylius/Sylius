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

use Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider\CaptureCommandProvider;
use Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider\NotifyCommandProvider;
use Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider\PayumActionsCommandProvider;
use Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider\StatusCommandProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_payum.command_provider.payment_request.offline', PayumActionsCommandProvider::class)
        ->decorate('sylius.command_provider.payment_request.offline')
        ->args([
            service('.inner'),
            tagged_locator('sylius_payum.command_provider.payment_request.offline', indexAttribute: 'action'),
        ]);

    $services->set('sylius_payum.command_provider.payment_request.offline.capture', CaptureCommandProvider::class)
        ->tag('sylius_payum.command_provider.payment_request.offline', ['action' => 'capture']);

    $services->set('sylius_payum.command_provider.payment_request.offline.status', StatusCommandProvider::class)
        ->tag('sylius_payum.command_provider.payment_request.offline', ['action' => 'status']);

    $services->set('sylius_payum.command_provider.payment_request.offline.notify', NotifyCommandProvider::class)
        ->tag('sylius_payum.command_provider.payment_request.offline', ['action' => 'notify']);
};
