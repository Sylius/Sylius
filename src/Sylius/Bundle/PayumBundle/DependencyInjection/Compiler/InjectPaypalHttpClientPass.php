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

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class InjectPaypalHttpClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('payum.builder')) {
            return;
        }

        $payumBuilder = $container->findDefinition('payum.builder');
        $payumBuilder->addMethodCall('addGatewayFactoryConfig', [
            'paypal_express_checkout',
            ['payum.http_client' => new Reference('sylius.payum.http_client')],
        ]);
    }
}
