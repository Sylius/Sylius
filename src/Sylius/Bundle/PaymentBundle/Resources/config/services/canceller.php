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

use Sylius\Bundle\PaymentBundle\Canceller\PaymentRequestCanceller;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.canceller.payment_request', PaymentRequestCanceller::class)
        ->args([
            service('sylius.repository.payment_request'),
            service('sylius_abstraction.state_machine'),
            service('doctrine.orm.entity_manager'),
            '%sylius.payment_request.states_to_be_cancelled_when_payment_method_changed%',
        ]);

    $services->alias(PaymentRequestCancellerInterface::class, 'sylius.canceller.payment_request');
};
