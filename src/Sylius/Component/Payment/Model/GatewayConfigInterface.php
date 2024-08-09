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

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface GatewayConfigInterface extends ResourceInterface
{
    public function getGatewayName(): ?string;

    /** @param string $gatewayName */
    public function setGatewayName($gatewayName): void;

    public function getFactoryName(): ?string;

    /** @param string $factoryName */
    public function setFactoryName($factoryName): void;

    /** @return array<string, mixed> */
    public function getConfig(): array;

    /** @param array<string, mixed> $config */
    public function setConfig(array $config): void;
}
