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

use Sylius\Bundle\PayumBundle\Extension\UpdatePaymentStateExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_payum.extension.update_payment_state', UpdatePaymentStateExtension::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('payum.extension', ['all' => true, 'prepend' => true]);
};
