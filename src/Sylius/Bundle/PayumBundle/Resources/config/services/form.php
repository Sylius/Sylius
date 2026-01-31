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

use Sylius\Bundle\PayumBundle\Form\Extension\CryptedGatewayConfigTypeExtension;
use Sylius\Bundle\PayumBundle\Form\Extension\PayumGatewayConfigTypeExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_payum.form.extension.type.payum_gateway_config', PayumGatewayConfigTypeExtension::class)
        ->args([
            service('payum'),
            service('sylius.command_provider.payment_request.default'),
        ])
        ->tag('form.type_extension', ['priority' => 200]);

    $services->set('sylius_payum.form.extension.type.crypted_gateway_config', CryptedGatewayConfigTypeExtension::class)
        ->args([
            service('sylius_payum.checker.payum_gateway_config_encryption'),
            service('payum.dynamic_gateways.cypher')->nullOnInvalid(),
        ])
        ->tag('form.type_extension', ['priority' => 100]);
};
