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

use Sylius\Bundle\PaymentBundle\Checker\GatewayConfigEncryptionCheckerInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface as BaseGatewayConfigInterface;

/** @experimental */
final readonly class GatewayConfigEncryptionChecker implements GatewayConfigEncryptionCheckerInterface
{
    public function __construct(
        private GatewayConfigEncryptionCheckerInterface $decorated,
    ) {
    }

    /** @param GatewayConfigInterface $gatewayConfig */
    public function isEncryptionEnabled(BaseGatewayConfigInterface $gatewayConfig): bool
    {
        return
            $this->decorated->isEncryptionEnabled($gatewayConfig) &&
            !$gatewayConfig->getUsePayum()
        ;
    }
}
