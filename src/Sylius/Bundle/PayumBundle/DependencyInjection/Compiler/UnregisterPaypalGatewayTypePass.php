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

use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class UnregisterPaypalGatewayTypePass implements CompilerPassInterface
{
    private const PAYPAL_GATEWAY_TYPE_SERVICE_ID = 'sylius.form.type.gateway_configuration.paypal';

    private const PAYPAL_CONVERT_ACTION_SERVICE_ID = 'sylius.payum_action.paypal_express_checkout.convert_payment';

    public function process(ContainerBuilder $container): void
    {
        if (class_exists(PaypalExpressCheckoutGatewayFactory::class)) {
            return;
        }

        $container->removeDefinition(self::PAYPAL_GATEWAY_TYPE_SERVICE_ID);
        $container->removeDefinition(self::PAYPAL_CONVERT_ACTION_SERVICE_ID);
    }
}
