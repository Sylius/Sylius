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

use Sylius\Bundle\PayumBundle\PaymentRequest\Resolver\DoctrineProxyObjectResolver;
use Sylius\Bundle\PayumBundle\PaymentRequest\Resolver\DoctrineProxyObjectResolverInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_payum.resolver.payment_request.doctrine_proxy_object', DoctrineProxyObjectResolver::class)
        ->args([service('doctrine.orm.entity_manager')])
    ;
    $services->alias(DoctrineProxyObjectResolverInterface::class, 'sylius_payum.resolver.payment_request.doctrine_proxy_object');
};
