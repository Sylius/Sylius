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

use Payum\Stripe\StripeCheckoutGatewayFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class UnregisterStripeGatewayTypePass implements CompilerPassInterface
{
    private const STRIPE_GATEWAY_TYPE_SERVICE_ID = 'sylius.form.type.gateway_configuration.stripe';

    public function process(ContainerBuilder $container): void
    {
        if (class_exists(StripeCheckoutGatewayFactory::class)) {
            return;
        }

        $container->removeDefinition(self::STRIPE_GATEWAY_TYPE_SERVICE_ID);
    }
}
