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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Provider;

use Sylius\Component\Core\Model\PaymentMethodInterface;

final class GatewayFactoryNameProvider implements GatewayFactoryNameProviderInterface
{
    public function provide(PaymentMethodInterface $paymentMethod): ?string
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            return null;
        }

        return $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();
    }
}
