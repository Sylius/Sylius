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

use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestChecker;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Bundle\PaymentBundle\Checker\PaymentRequestDuplicationChecker;
use Sylius\Bundle\PaymentBundle\Checker\PaymentRequestDuplicationCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.checker.payment_request_duplication', PaymentRequestDuplicationChecker::class)
        ->args([service('sylius.repository.payment_request')]);

    $services->alias(PaymentRequestDuplicationCheckerInterface::class, 'sylius.checker.payment_request_duplication');

    $services->set('sylius.checker.finalized_payment_request', FinalizedPaymentRequestChecker::class)
        ->args([service('sylius_abstraction.state_machine')]);

    $services->alias(FinalizedPaymentRequestCheckerInterface::class, 'sylius.checker.finalized_payment_request');
};
