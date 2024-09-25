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

use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class DefaultActionProvider implements DefaultActionProviderInterface
{
    public function getAction(PaymentRequestInterface $paymentRequest): string
    {
        $paymentMethod = $paymentRequest->getMethod();
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $authorize = $gatewayConfig->getConfig()['use_authorize'] ?? false;

        return $authorize ? PaymentRequestInterface::ACTION_AUTHORIZE : $paymentRequest->getAction();

    }
}
