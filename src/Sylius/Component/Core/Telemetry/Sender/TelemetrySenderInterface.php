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

namespace Sylius\Component\Core\Telemetry\Sender;

/** @internal */
interface TelemetrySenderInterface
{
    /**
     * @param array<string, mixed> $telemetryData
     * @return bool True if sent successfully, false otherwise
     */
    public function send(array $telemetryData): bool;
}
