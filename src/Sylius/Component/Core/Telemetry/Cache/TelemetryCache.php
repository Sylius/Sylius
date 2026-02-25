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

use Psr\Cache\CacheItemPoolInterface;

/** @internal */
final class TelemetryCache implements TelemetryCacheInterface
{
    private const CACHE_KEY = 'sylius_telemetry';
    private const CACHE_TTL_SUCCESS = 604800; // 7 days
    private const CACHE_TTL_FAILURE = 259200; // 3 days
    private const RETRY_DELAY = 86400; // 24 hours between retries
    private const MAX_ATTEMPTS = 3;
    private const STATUS_SUCCESS = 'success';
    private const STATUS_FAILED = 'failed';

    /** @var CacheItemPoolInterface */
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function shouldSendTelemetry(): bool
    {
        $item = $this->cache->getItem(self::CACHE_KEY);

        if (!$item->isHit()) {
            return true;
        }

        $data = $item->get();
        $status = $data['status'] ?? null;

        if ($status === self::STATUS_SUCCESS) {
            return false;
        }

        if ($status === self::STATUS_FAILED) {
            $attempts = $data['attempts'] ?? 0;

            if ($attempts >= self::MAX_ATTEMPTS) {
                return false;
            }

            $lastAttemptAt = $data['last_attempt_at'] ?? null;

            if ($lastAttemptAt === null) {
                return true;
            }

            $lastAttempt = new \DateTimeImmutable($lastAttemptAt, new \DateTimeZone('UTC'));
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $secondsSinceLastAttempt = $now->getTimestamp() - $lastAttempt->getTimestamp();

            return $secondsSinceLastAttempt >= self::RETRY_DELAY;
        }

        return true;
    }

    /** @return array<string, mixed>|null */
    public function getCachedTelemetryData(): ?array
    {
        $item = $this->cache->getItem(self::CACHE_KEY);

        if (!$item->isHit()) {
            return null;
        }

        $data = $item->get();

        return $data['telemetry_data'] ?? null;
    }

    public function storeSuccess(string $installationId): void
    {
        $item = $this->cache->getItem(self::CACHE_KEY);
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DATE_ATOM);

        $item->set([
            'status' => self::STATUS_SUCCESS,
            'installation_id' => $installationId,
            'sent_at' => $now,
        ]);
        $item->expiresAfter(self::CACHE_TTL_SUCCESS);

        $this->cache->save($item);
    }

    /** @param array<string, mixed> $telemetryData */
    public function storeFailure(string $installationId, array $telemetryData): void
    {
        $item = $this->cache->getItem(self::CACHE_KEY);
        $existingData = $item->isHit() ? $item->get() : [];
        $attempts = ($existingData['attempts'] ?? 0) + 1;
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DATE_ATOM);

        $item->set([
            'status' => self::STATUS_FAILED,
            'installation_id' => $installationId,
            'attempts' => $attempts,
            'last_attempt_at' => $now,
            'telemetry_data' => $telemetryData,
        ]);
        $item->expiresAfter(self::CACHE_TTL_FAILURE);

        $this->cache->save($item);
    }

    public function clear(): void
    {
        $this->cache->deleteItem(self::CACHE_KEY);
    }
}
