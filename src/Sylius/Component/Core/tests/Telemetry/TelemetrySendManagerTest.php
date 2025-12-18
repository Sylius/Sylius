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

namespace Tests\Sylius\Component\Core\Telemetry;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Cache\TelemetryCacheInterface;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;
use Sylius\Component\Core\Telemetry\TelemetryOrchestratorInterface;
use Sylius\Component\Core\Telemetry\TelemetrySendManager;
use Symfony\Component\HttpFoundation\Request;

final class TelemetrySendManagerTest extends TestCase
{
    /** @var TelemetryOrchestratorInterface&MockObject */
    private TelemetryOrchestratorInterface $orchestrator;

    /** @var TelemetryCacheInterface&MockObject */
    private TelemetryCacheInterface $cache;

    /** @var TelemetrySenderInterface&MockObject */
    private TelemetrySenderInterface $sender;

    private TelemetrySendManager $manager;

    private Request $request;

    protected function setUp(): void
    {
        $this->orchestrator = $this->createMock(TelemetryOrchestratorInterface::class);
        $this->cache = $this->createMock(TelemetryCacheInterface::class);
        $this->sender = $this->createMock(TelemetrySenderInterface::class);
        $this->manager = new TelemetrySendManager($this->orchestrator, $this->cache, $this->sender);
        $this->request = Request::create('https://example.com/admin');
    }

    public function test_it_does_nothing_when_should_not_send(): void
    {
        $this->cache->method('shouldSendTelemetry')->willReturn(false);

        $this->orchestrator->expects($this->never())->method('getData');
        $this->sender->expects($this->never())->method('send');

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_uses_cached_data_when_available(): void
    {
        $cachedData = [
            'installation_id' => 'cached-id',
            'data' => 'cached',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn($cachedData);
        $this->sender->method('send')->willReturn(true);

        $this->orchestrator->expects($this->never())->method('getData');
        $this->sender->expects($this->once())->method('send')->with($cachedData);
        $this->cache->expects($this->once())->method('storeSuccess')->with('cached-id');

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_generates_new_data_when_no_cache(): void
    {
        $telemetryData = [
            'installation_id' => 'test-id',
            'collected_at' => '2024-01-01T00:00:00+00:00',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn($telemetryData);
        $this->sender->method('send')->willReturn(true);

        $this->orchestrator->expects($this->once())->method('getData');
        $this->sender->expects($this->once())->method('send')->with($telemetryData);
        $this->cache->expects($this->once())->method('storeSuccess')->with('test-id');

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_does_nothing_when_installation_id_is_empty(): void
    {
        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn(['installation_id' => '']);

        $this->sender->expects($this->never())->method('send');
        $this->cache->expects($this->never())->method('storeSuccess');
        $this->cache->expects($this->never())->method('storeFailure');

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_stores_success_when_send_succeeds(): void
    {
        $telemetryData = [
            'installation_id' => 'test-id',
            'data' => 'test',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn($telemetryData);
        $this->sender->method('send')->willReturn(true);

        $this->cache->expects($this->once())->method('storeSuccess')->with('test-id');
        $this->cache->expects($this->never())->method('storeFailure');

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_stores_failure_when_send_fails(): void
    {
        $telemetryData = [
            'installation_id' => 'test-id',
            'data' => 'test',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn($telemetryData);
        $this->sender->method('send')->willReturn(false);

        $this->cache->expects($this->never())->method('storeSuccess');
        $this->cache->expects($this->once())->method('storeFailure')->with('test-id', $telemetryData);

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_stores_failure_when_send_throws_exception(): void
    {
        $telemetryData = [
            'installation_id' => 'test-id',
            'data' => 'test',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn($telemetryData);
        $this->sender->method('send')->willThrowException(new \RuntimeException('Network error'));

        $this->cache->expects($this->never())->method('storeSuccess');
        $this->cache->expects($this->once())->method('storeFailure')->with('test-id', $telemetryData);

        $this->manager->sendIfNeeded($this->request);
    }

    public function test_it_sends_only_once_per_request(): void
    {
        $telemetryData = [
            'installation_id' => 'test-id',
            'data' => 'test',
        ];

        $this->cache->method('shouldSendTelemetry')->willReturn(true);
        $this->cache->method('getCachedTelemetryData')->willReturn(null);
        $this->orchestrator->method('getData')->with($this->request)->willReturn($telemetryData);
        $this->sender->method('send')->willReturn(false);

        $this->sender->expects($this->once())->method('send');

        $this->manager->sendIfNeeded($this->request);
    }
}
