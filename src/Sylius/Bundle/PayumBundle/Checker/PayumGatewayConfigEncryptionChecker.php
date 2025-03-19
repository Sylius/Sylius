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

namespace Sylius\Bundle\PayumBundle\Checker;

use Payum\Core\Security\CryptedInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface as BaseGatewayConfigInterface;

/** @experimental */
final readonly class PayumGatewayConfigEncryptionChecker implements PayumGatewayConfigEncryptionCheckerInterface
{
    /** @param GatewayConfigInterface $gatewayConfig */
    public function isPayumEncryptionEnabled(BaseGatewayConfigInterface $gatewayConfig): bool
    {
        return
            $gatewayConfig instanceof CryptedInterface &&
            $gatewayConfig->getUsePayum()
        ;
    }
}
