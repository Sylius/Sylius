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

use Sylius\Bundle\PaymentBundle\Processor\HttpResponseProcessor;
use Sylius\Bundle\PaymentBundle\Processor\HttpResponseProcessorInterface;
use Sylius\Bundle\PaymentBundle\Processor\NotifyPayloadProcessor;
use Sylius\Bundle\PaymentBundle\Processor\NotifyPayloadProcessorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.processor.payment_request.notify_payload', NotifyPayloadProcessor::class)
        ->args([service('sylius.normalizer.symfony_request')])
    ;
    $services->alias(NotifyPayloadProcessorInterface::class, 'sylius.processor.payment_request.notify_payload');

    $services
        ->set('sylius.processor.payment_request.http_response', HttpResponseProcessor::class)
        ->args([
            service('sylius.announcer.payment_request'),
            service('sylius.provider.http_response.default'),
        ])
    ;
    $services->alias(HttpResponseProcessorInterface::class, 'sylius.processor.payment_request.http_response');
};
