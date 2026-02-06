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

use Sylius\Bundle\PaymentBundle\EventListener\PaymentMethodChangeEventListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.listener.payment_method_change', PaymentMethodChangeEventListener::class)
        ->args([service('sylius.canceller.payment_request')])
        ->tag('doctrine.event_listener', ['event' => 'postUpdate']);
};
