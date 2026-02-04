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

use Sylius\Bundle\PayumBundle\PaymentRequest\Factory\PayumTokenFactory;
use Sylius\Bundle\PayumBundle\PaymentRequest\Factory\PayumTokenFactoryInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_payum.factory.payment_request.payum_token', PayumTokenFactory::class)
        ->args([service('payum')]);

    $services->alias(PayumTokenFactoryInterface::class, 'sylius_payum.factory.payment_request.payum_token');
};
