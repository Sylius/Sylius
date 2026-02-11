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

use Sylius\Bundle\PayumBundle\Factory\AuthorizeFactory;
use Sylius\Bundle\PayumBundle\Factory\CaptureFactory;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactory;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactory;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_payum.factory.get_status', GetStatusFactory::class);
    $services->alias(GetStatusFactoryInterface::class, 'sylius_payum.factory.get_status');

    $services->set('sylius_payum.factory.resolve_next_route', ResolveNextRouteFactory::class);
    $services->alias(ResolveNextRouteFactoryInterface::class, 'sylius_payum.factory.resolve_next_route');

    $services->set('sylius_payum.factory.capture', CaptureFactory::class);

    $services->set('sylius_payum.factory.authorize', AuthorizeFactory::class);
};
