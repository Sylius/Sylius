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

namespace Sylius\Component\Core\Telemetry;

use Sylius\Component\Core\Telemetry\Collector\TelemetryDataCollectorInterface;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGeneratorInterface;

/** @internal */
final class TelemetryOrchestrator implements TelemetryOrchestratorInterface
{
    private const SCHEMA_VERSION = 2;

    /**
     * @param iterable<TelemetryDataCollectorInterface> $collectors
     */
    public function __construct(
        private InstallationIdGeneratorInterface $installationIdGenerator,
        private iterable $collectors,
    ) {
    }

    public function getData(): array
    {
        try {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $oneMonthAgo = $now->modify('-1 month');

            $data = [
                'schema_version' => self::SCHEMA_VERSION,
                'installation_id' => $this->installationIdGenerator->generate(),
                'collected_at' => $now->format(\DATE_ATOM),
                'period' => [
                    'start' => $oneMonthAgo->format(\DATE_ATOM),
                    'end' => $now->format(\DATE_ATOM),
                ],
            ];

            foreach ($this->collectors as $collector) {
                if (!$collector instanceof TelemetryDataCollectorInterface) {
                    continue;
                }

                try {
                    if (!$collector->isEnabled()) {
                        continue;
                    }

                    $data[$collector->getName()] = $collector->collect();
                } catch (\Throwable) {
                }
            }

            return $data;
        } catch (\Throwable) {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

            return [
                'schema_version' => self::SCHEMA_VERSION,
                'installation_id' => '',
                'collected_at' => $now->format(\DATE_ATOM),
                'period' => [
                    'start' => $now->modify('-1 month')->format(\DATE_ATOM),
                    'end' => $now->format(\DATE_ATOM),
                ],
            ];
        }
    }
}
