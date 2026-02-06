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

use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessor;
use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessorInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.processor.order_pay.route_parameters', RouteParametersProcessor::class)
        ->args([
            inline_service(ExpressionLanguage::class),
            service('router'),
        ]);

    $services->alias(RouteParametersProcessorInterface::class, 'sylius.processor.order_pay.route_parameters');
};
