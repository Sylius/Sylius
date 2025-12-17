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

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Component\Payment\Model\GatewayConfigInterface;

/** @experimental */
final readonly class GatewayConfigEncryptionChecker implements GatewayConfigEncryptionCheckerInterface
{
    /**
     * @param array<string> $disabledGatewayFactories
     */
    public function __construct(
        private array $disabledGatewayFactories,
    ) {
    }

    public function isEncryptionEnabled(GatewayConfigInterface $gatewayConfig): bool
    {
        return !in_array($gatewayConfig->getFactoryName(), $this->disabledGatewayFactories, true);
    }
}
