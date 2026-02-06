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

use Sylius\Bundle\PaymentBundle\Checker\GatewayConfigEncryptionChecker;
use Sylius\Bundle\PaymentBundle\Checker\GatewayConfigEncryptionCheckerInterface;
use Sylius\Bundle\PaymentBundle\Listener\EntityEncryptionListener;
use Sylius\Bundle\PaymentBundle\Listener\GatewayConfigEncryptionListener;
use Sylius\Component\Payment\Encryption\Encrypter;
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\GatewayConfigEncrypter;
use Sylius\Component\Payment\Encryption\PaymentRequestEncrypter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.checker.gateway_config_encryption', GatewayConfigEncryptionChecker::class)
        ->args(['%sylius.encryption.disabled_for_factories%'])
    ;
    $services->alias(GatewayConfigEncryptionCheckerInterface::class, 'sylius.checker.gateway_config_encryption');

    $services->set('sylius.encrypter', Encrypter::class)->args(['%env(resolve:SYLIUS_PAYMENT_ENCRYPTION_KEY_PATH)%']);
    $services->alias(EncrypterInterface::class, 'sylius.encrypter');

    $services
        ->set('sylius.encrypter.payment_request', PaymentRequestEncrypter::class)
        ->args([service('sylius.encrypter')])
    ;

    $services
        ->set('sylius.encrypter.gateway_config', GatewayConfigEncrypter::class)
        ->args([service('sylius.encrypter')])
    ;

    $services
        ->set('sylius.listener.gateway_config_encryption', GatewayConfigEncryptionListener::class)
        ->args([
            service('sylius.encrypter.gateway_config'),
            '%sylius.model.gateway_config.class%',
            service('sylius.checker.gateway_config_encryption'),
        ])
        ->tag('doctrine.event_listener', ['event' => 'onFlush'])
        ->tag('doctrine.event_listener', ['event' => 'postFlush'])
        ->tag('doctrine.event_listener', ['event' => 'postLoad'])
    ;

    $services
        ->set('sylius.listener.payment_request_encryption', EntityEncryptionListener::class)
        ->args([
            service('sylius.encrypter.payment_request'),
            '%sylius.model.payment_request.class%',
        ])
        ->tag('doctrine.event_listener', ['event' => 'onFlush'])
        ->tag('doctrine.event_listener', ['event' => 'postFlush'])
        ->tag('doctrine.event_listener', ['event' => 'postLoad'])
    ;
};
