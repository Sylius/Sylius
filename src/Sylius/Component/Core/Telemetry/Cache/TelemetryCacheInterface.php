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

namespace Sylius\Component\Core\Telemetry\Cache;

/** @internal */
interface TelemetryCacheInterface
{
    public function shouldSendTelemetry(): bool;

    /** @return array<string, mixed>|null */
    public function getCachedTelemetryData(): ?array;

    public function storeSuccess(string $installationId): void;

    /** @param array<string, mixed> $telemetryData */
    public function storeFailure(string $installationId, array $telemetryData): void;

    public function clear(): void;
}
