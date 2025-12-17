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

use Sylius\Component\Core\Telemetry\Cache\TelemetryCacheInterface;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;
use Symfony\Component\HttpFoundation\Request;

/** @internal */
final class TelemetrySendManager implements TelemetrySendManagerInterface
{
    public function __construct(
        private TelemetryOrchestratorInterface $telemetryOrchestrator,
        private TelemetryCacheInterface $telemetryCache,
        private TelemetrySenderInterface $telemetrySender,
    ) {
    }

    public function sendIfNeeded(Request $request): void
    {
        if (!$this->telemetryCache->shouldSendTelemetry()) {
            return;
        }

        $data = $this->telemetryCache->getCachedTelemetryData() ?? $this->telemetryOrchestrator->getData($request);

        $installationId = $data['installation_id'] ?? '';

        if ('' === $installationId) {
            return;
        }

        $sent = $this->send($data);

        if ($sent) {
            $this->telemetryCache->storeSuccess($installationId);
        } else {
            $this->telemetryCache->storeFailure($installationId, $data);
        }
    }

    /** @param array<string, mixed> $data */
    private function send(array $data): bool
    {
        try {
            return $this->telemetrySender->send($data);
        } catch (\Throwable) {
            return false;
        }
    }
}
