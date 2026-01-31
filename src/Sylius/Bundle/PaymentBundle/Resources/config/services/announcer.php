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

use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncer;
use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.announcer.payment_request', PaymentRequestAnnouncer::class)
        ->args([
            service('sylius.checker.finalized_payment_request'),
            service('sylius.command_provider.payment_request.default'),
            service('sylius.payment_request.command_bus'),
        ]);

    $services->alias(PaymentRequestAnnouncerInterface::class, 'sylius.announcer.payment_request');
};
