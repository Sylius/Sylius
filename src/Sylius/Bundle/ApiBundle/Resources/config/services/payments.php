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

use Sylius\Bundle\ApiBundle\Controller\Payment\GetPaymentConfiguration;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.controller.payment.get_payment_configuration', GetPaymentConfiguration::class)
        ->args([
            service('sylius.repository.payment'),
            service('sylius_api.provider.payment_configuration'),
        ]);
};
