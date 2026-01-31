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

use Sylius\Bundle\PayumBundle\Action\AuthorizePaymentAction;
use Sylius\Bundle\PayumBundle\Action\CapturePaymentAction;
use Sylius\Bundle\PayumBundle\Action\ExecuteSameRequestWithPaymentDetailsAction;
use Sylius\Bundle\PayumBundle\Action\Offline\ConvertPaymentAction;
use Sylius\Bundle\PayumBundle\Action\Offline\ResolveNextRouteAction as ResolveNextOfflineRouteAction;
use Sylius\Bundle\PayumBundle\Action\Offline\StatusAction;
use Sylius\Bundle\PayumBundle\Action\ResolveNextRouteAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_payum.action.authorize_payment', AuthorizePaymentAction::class)
        ->args([service('sylius_payum.provider.payment_description')])
        ->tag('payum.action', ['all' => true, 'alias' => 'sylius.authorize_payment']);

    $services->set('sylius_payum.action.capture_payment', CapturePaymentAction::class)
        ->args([service('sylius_payum.provider.payment_description')])
        ->tag('payum.action', ['all' => true, 'alias' => 'sylius.capture_payment']);

    $services->set('sylius_payum.action.execute_same_request_with_payment_details', ExecuteSameRequestWithPaymentDetailsAction::class)
        ->tag('payum.action', ['all' => true]);

    $services->set('sylius_payum.action.resolve_next_route', ResolveNextRouteAction::class)
        ->tag('payum.action', ['all' => true, 'alias' => 'sylius.resolve_next_route']);

    $services->set('sylius_payum.action.offline.convert_payment', ConvertPaymentAction::class)
        ->tag('payum.action', ['factory' => 'offline', 'alias' => 'sylius.offline.convert_payment']);

    $services->set('sylius_payum.action.offline.status', StatusAction::class)
        ->tag('payum.action', ['factory' => 'offline', 'alias' => 'sylius.offline.status']);

    $services->set('sylius_payum.action.offline.resolve_next_route', ResolveNextOfflineRouteAction::class)
        ->tag('payum.action', ['factory' => 'offline', 'alias' => 'sylius.offline.resolve_next_route']);
};
