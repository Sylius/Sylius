<?php

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
