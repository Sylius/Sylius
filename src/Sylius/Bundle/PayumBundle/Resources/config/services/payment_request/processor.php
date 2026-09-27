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

use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\AfterTokenRequestProcessor;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\AfterTokenRequestProcessorInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\RequestProcessor;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\RequestProcessorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_payum.processor.payment_request.after_token_request', AfterTokenRequestProcessor::class)
        ->args([
            service('sylius.factory.payment_request'),
            service('sylius.repository.payment_request'),
            service('sylius.announcer.payment_request'),
        ])
    ;
    $services->alias(AfterTokenRequestProcessorInterface::class, 'sylius_payum.processor.payment_request.after_token_request');

    $services
        ->set('sylius_payum.processor.payment_request.request', RequestProcessor::class)
        ->args([
            service('payum'),
            service('sylius_payum.context.payment_request'),
            service('sylius_abstraction.state_machine'),
        ])
    ;
    $services->alias(RequestProcessorInterface::class, 'sylius_payum.processor.payment_request.request');
};
