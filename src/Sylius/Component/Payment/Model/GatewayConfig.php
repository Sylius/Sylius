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

class GatewayConfig implements GatewayConfigInterface
{
    protected mixed $id;

    protected ?string $factoryName = null;

    protected ?string $gatewayName = null;

    /** @var array<string, mixed> $config */
    protected array $config;

    public function __construct()
    {
        $this->config = [];
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getGatewayName(): ?string
    {
        return $this->gatewayName;
    }

    public function setGatewayName($gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }

    public function getFactoryName(): ?string
    {
        return $this->factoryName;
    }

    public function setFactoryName($factoryName): void
    {
        $this->factoryName = $factoryName;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
