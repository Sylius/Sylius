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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Collector\TelemetryDataCollectorInterface;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGeneratorInterface;
use Sylius\Component\Core\Telemetry\TelemetryOrchestrator;
use Symfony\Component\HttpFoundation\Request;

#[AllowMockObjectsWithoutExpectations]
final class TelemetryOrchestratorTest extends TestCase
{
    private Request $request;

    protected function setUp(): void
    {
        $this->request = Request::create('https://example.com');
    }

    public function test_it_generates_state_with_installation_id_and_timestamp(): void
    {
        $installationIdGenerator = $this->createMock(InstallationIdGeneratorInterface::class);
        $installationIdGenerator->method('generate')->willReturn('install-uuid');

        $collector = $this->createMock(TelemetryDataCollectorInterface::class);
        $collector->method('getName')->willReturn('technical');
        $collector->method('isEnabled')->willReturn(true);
        $collector->method('collect')->willReturn([
            'sylius_version' => '1.12.0',
            'php_version' => '8.2.0',
        ]);

        $orchestrator = new TelemetryOrchestrator($installationIdGenerator, [$collector]);

        $data = $orchestrator->getData($this->request);

        self::assertArrayHasKey('schema_version', $data);
        self::assertArrayHasKey('installation_id', $data);
        self::assertArrayHasKey('collected_at', $data);
        self::assertArrayHasKey('period', $data);
        self::assertArrayHasKey('start', $data['period']);
        self::assertArrayHasKey('end', $data['period']);
        self::assertArrayHasKey('technical', $data);
        self::assertSame(2, $data['schema_version']);
        self::assertSame('install-uuid', $data['installation_id']);
        self::assertNotEmpty($data['collected_at']);
        self::assertNotEmpty($data['period']['start']);
        self::assertNotEmpty($data['period']['end']);
        self::assertSame('1.12.0', $data['technical']['sylius_version']);
    }

    public function test_it_returns_empty_installation_id_on_error(): void
    {
        $installationIdGenerator = $this->createMock(InstallationIdGeneratorInterface::class);
        $installationIdGenerator->method('generate')->willThrowException(new \RuntimeException('Test error'));

        $collector = $this->createMock(TelemetryDataCollectorInterface::class);
        $collector->method('getName')->willReturn('technical');
        $collector->method('isEnabled')->willReturn(true);
        $collector->method('collect')->willReturn([]);

        $orchestrator = new TelemetryOrchestrator($installationIdGenerator, [$collector]);

        $data = $orchestrator->getData($this->request);

        self::assertIsArray($data);
        self::assertArrayHasKey('schema_version', $data);
        self::assertArrayHasKey('installation_id', $data);
        self::assertArrayHasKey('collected_at', $data);
        self::assertArrayHasKey('period', $data);
        self::assertSame(2, $data['schema_version']);
        self::assertSame('', $data['installation_id']);
    }

    public function test_it_collects_data_from_multiple_collectors(): void
    {
        $installationIdGenerator = $this->createMock(InstallationIdGeneratorInterface::class);
        $installationIdGenerator->method('generate')->willReturn('install-uuid');

        $technicalCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $technicalCollector->method('getName')->willReturn('technical');
        $technicalCollector->method('isEnabled')->willReturn(true);
        $technicalCollector->method('collect')->willReturn([
            'sylius_version' => '1.12.0',
        ]);

        $businessCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $businessCollector->method('getName')->willReturn('business');
        $businessCollector->method('isEnabled')->willReturn(true);
        $businessCollector->method('collect')->willReturn([
            'orders_count' => 100,
        ]);

        $orchestrator = new TelemetryOrchestrator($installationIdGenerator, [$technicalCollector, $businessCollector]);

        $data = $orchestrator->getData($this->request);

        self::assertArrayHasKey('technical', $data);
        self::assertArrayHasKey('business', $data);
        self::assertSame('1.12.0', $data['technical']['sylius_version']);
        self::assertSame(100, $data['business']['orders_count']);
    }

    public function test_it_continues_on_collector_error(): void
    {
        $installationIdGenerator = $this->createMock(InstallationIdGeneratorInterface::class);
        $installationIdGenerator->method('generate')->willReturn('install-uuid');

        $failingCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $failingCollector->method('getName')->willReturn('failing');
        $failingCollector->method('isEnabled')->willReturn(true);
        $failingCollector->method('collect')->willThrowException(new \RuntimeException('Collector error'));

        $workingCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $workingCollector->method('getName')->willReturn('technical');
        $workingCollector->method('isEnabled')->willReturn(true);
        $workingCollector->method('collect')->willReturn([
            'sylius_version' => '1.12.0',
        ]);

        $orchestrator = new TelemetryOrchestrator($installationIdGenerator, [$failingCollector, $workingCollector]);

        $data = $orchestrator->getData($this->request);

        self::assertArrayHasKey('technical', $data);
        self::assertSame('1.12.0', $data['technical']['sylius_version']);
    }

    public function test_it_skips_disabled_collectors(): void
    {
        $installationIdGenerator = $this->createMock(InstallationIdGeneratorInterface::class);
        $installationIdGenerator->method('generate')->willReturn('install-uuid');

        $disabledCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $disabledCollector->method('getName')->willReturn('business');
        $disabledCollector->method('isEnabled')->willReturn(false);
        $disabledCollector->expects($this->never())->method('collect');

        $enabledCollector = $this->createMock(TelemetryDataCollectorInterface::class);
        $enabledCollector->method('getName')->willReturn('technical');
        $enabledCollector->method('isEnabled')->willReturn(true);
        $enabledCollector->method('collect')->willReturn([
            'sylius_version' => '1.12.0',
        ]);

        $orchestrator = new TelemetryOrchestrator($installationIdGenerator, [$disabledCollector, $enabledCollector]);

        $data = $orchestrator->getData($this->request);

        self::assertArrayHasKey('technical', $data);
        self::assertArrayNotHasKey('business', $data);
    }
}
