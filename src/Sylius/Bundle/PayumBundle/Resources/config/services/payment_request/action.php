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

use Sylius\Bundle\PayumBundle\PaymentRequest\Action\SyliusGetHttpRequestAction;
use Sylius\Bundle\PayumBundle\PaymentRequest\Action\SyliusRenderTemplateAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_payum.action.payment_request.get_http_request', SyliusGetHttpRequestAction::class)
        ->args([service('sylius_payum.context.payment_request')])
        ->tag('payum.action', ['all' => true, 'prepend' => true]);

    $services->set('sylius_payum.action.payment_request.render_template', SyliusRenderTemplateAction::class)
        ->args([service('sylius_payum.context.payment_request')])
        ->tag('payum.action', ['all' => true, 'prepend' => true]);
};
