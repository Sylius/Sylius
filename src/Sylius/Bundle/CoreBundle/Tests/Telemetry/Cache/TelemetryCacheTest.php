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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Cache;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Cache\TelemetryCache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class TelemetryCacheTest extends TestCase
{
    private ArrayAdapter $cache;

    private TelemetryCache $telemetryCache;

    protected function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->telemetryCache = new TelemetryCache($this->cache);
    }

    public function test_should_send_telemetry_returns_true_when_no_cache(): void
    {
        self::assertTrue($this->telemetryCache->shouldSendTelemetry());
    }

    public function test_should_send_telemetry_returns_false_after_success(): void
    {
        $this->telemetryCache->storeSuccess('test-id');

        self::assertFalse($this->telemetryCache->shouldSendTelemetry());
    }

    public function test_should_send_telemetry_returns_false_when_retry_delay_not_passed(): void
    {
        $telemetryData = ['installation_id' => 'test-id', 'data' => 'test'];
        $this->telemetryCache->storeFailure('test-id', $telemetryData);

        self::assertFalse($this->telemetryCache->shouldSendTelemetry());
    }

    public function test_should_send_telemetry_returns_false_after_max_attempts(): void
    {
        $telemetryData = ['installation_id' => 'test-id', 'data' => 'test'];

        $this->telemetryCache->storeFailure('test-id', $telemetryData);
        $this->telemetryCache->storeFailure('test-id', $telemetryData);
        $this->telemetryCache->storeFailure('test-id', $telemetryData);

        self::assertFalse($this->telemetryCache->shouldSendTelemetry());
    }

    public function test_get_cached_telemetry_data_returns_null_when_no_cache(): void
    {
        self::assertNull($this->telemetryCache->getCachedTelemetryData());
    }

    public function test_get_cached_telemetry_data_returns_null_after_success(): void
    {
        $this->telemetryCache->storeSuccess('test-id');

        self::assertNull($this->telemetryCache->getCachedTelemetryData());
    }

    public function test_get_cached_telemetry_data_returns_data_after_failure(): void
    {
        $telemetryData = ['installation_id' => 'test-id', 'some_data' => 'value'];
        $this->telemetryCache->storeFailure('test-id', $telemetryData);

        self::assertSame($telemetryData, $this->telemetryCache->getCachedTelemetryData());
    }

    public function test_store_success_saves_correct_data(): void
    {
        $this->telemetryCache->storeSuccess('test-id');

        $item = $this->cache->getItem('sylius_telemetry');
        $data = $item->get();

        self::assertSame('success', $data['status']);
        self::assertSame('test-id', $data['installation_id']);
        self::assertArrayHasKey('sent_at', $data);
    }

    public function test_store_failure_increments_attempts(): void
    {
        $telemetryData = ['installation_id' => 'test-id'];

        $this->telemetryCache->storeFailure('test-id', $telemetryData);
        $item = $this->cache->getItem('sylius_telemetry');
        self::assertSame(1, $item->get()['attempts']);

        $this->telemetryCache->storeFailure('test-id', $telemetryData);
        $item = $this->cache->getItem('sylius_telemetry');
        self::assertSame(2, $item->get()['attempts']);
    }

    public function test_store_failure_saves_telemetry_data(): void
    {
        $telemetryData = ['installation_id' => 'test-id', 'metrics' => ['orders' => 100]];
        $this->telemetryCache->storeFailure('test-id', $telemetryData);

        $item = $this->cache->getItem('sylius_telemetry');
        $data = $item->get();

        self::assertSame('failed', $data['status']);
        self::assertSame($telemetryData, $data['telemetry_data']);
        self::assertArrayHasKey('last_attempt_at', $data);
    }

    public function test_clear_removes_cache(): void
    {
        $this->telemetryCache->storeSuccess('test-id');
        self::assertFalse($this->telemetryCache->shouldSendTelemetry());

        $this->telemetryCache->clear();
        self::assertTrue($this->telemetryCache->shouldSendTelemetry());
    }
}
