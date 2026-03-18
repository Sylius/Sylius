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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\EventListener\TelemetryListener;
use Sylius\Component\Core\Telemetry\Cache\TelemetryCacheInterface;
use Sylius\Component\Core\Telemetry\TelemetrySendManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class TelemetryListenerTest extends TestCase
{
    /** @var TelemetrySendManagerInterface|MockObject */
    private $telemetrySendManager;
    /** @var TelemetryCacheInterface|MockObject */
    private $telemetryCache;
    /** @var TelemetryListener */
    private $listener;

    protected function setUp(): void
    {
        $this->telemetrySendManager = $this->createMock(TelemetrySendManagerInterface::class);
        $this->telemetryCache = $this->createMock(TelemetryCacheInterface::class);
        $this->listener = new TelemetryListener($this->telemetrySendManager, $this->telemetryCache, '/api/v2/admin');
    }

    public function test_it_does_nothing_when_not_admin_route(): void
    {
        $this->telemetryCache->expects($this->never())->method('wasRecentlyTriggered');
        $this->telemetrySendManager->expects($this->never())->method('sendIfNeeded');

        $event = $this->createEvent('sylius_shop_homepage', '/');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_does_nothing_when_route_is_null_and_path_not_admin(): void
    {
        $this->telemetryCache->expects($this->never())->method('wasRecentlyTriggered');
        $this->telemetrySendManager->expects($this->never())->method('sendIfNeeded');

        $event = $this->createEvent(null, '/shop/products');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_calls_telemetry_send_manager_on_admin_dashboard_visit(): void
    {
        $this->telemetryCache->expects($this->once())->method('wasRecentlyTriggered')->willReturn(false);
        $this->telemetryCache->expects($this->once())->method('markAsRecentlyTriggered');
        $this->telemetrySendManager->expects($this->once())->method('sendIfNeeded');

        $event = $this->createEvent('sylius_admin_dashboard', '/admin');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_calls_telemetry_send_manager_on_admin_order_index(): void
    {
        $this->telemetryCache->expects($this->once())->method('wasRecentlyTriggered')->willReturn(false);
        $this->telemetryCache->expects($this->once())->method('markAsRecentlyTriggered');
        $this->telemetrySendManager->expects($this->once())->method('sendIfNeeded');

        $event = $this->createEvent('sylius_admin_order_index', '/admin/orders');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_calls_telemetry_send_manager_on_admin_api_route(): void
    {
        $this->telemetryCache->expects($this->once())->method('wasRecentlyTriggered')->willReturn(false);
        $this->telemetryCache->expects($this->once())->method('markAsRecentlyTriggered');
        $this->telemetrySendManager->expects($this->once())->method('sendIfNeeded');

        $event = $this->createEvent('api_orders_admin_get_collection', '/api/v2/admin/orders');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_does_not_trigger_on_shop_api_route(): void
    {
        $this->telemetryCache->expects($this->never())->method('wasRecentlyTriggered');
        $this->telemetrySendManager->expects($this->never())->method('sendIfNeeded');

        $event = $this->createEvent('api_orders_shop_get_collection', '/api/v2/shop/orders');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_skips_when_telemetry_was_recently_triggered(): void
    {
        $this->telemetryCache->expects($this->once())->method('wasRecentlyTriggered')->willReturn(true);
        $this->telemetryCache->expects($this->never())->method('markAsRecentlyTriggered');
        $this->telemetrySendManager->expects($this->never())->method('sendIfNeeded');

        $event = $this->createEvent('sylius_admin_dashboard', '/admin');

        $this->listener->onAdminAccess($event);
    }

    public function test_it_handles_exceptions_silently(): void
    {
        $this->telemetryCache->expects($this->once())->method('wasRecentlyTriggered')->willReturn(false);
        $this->telemetryCache->expects($this->once())->method('markAsRecentlyTriggered');
        $this->telemetrySendManager->method('sendIfNeeded')->willThrowException(new \RuntimeException('Test exception'));

        $event = $this->createEvent('sylius_admin_dashboard', '/admin');

        $this->listener->onAdminAccess($event);

        $this->assertTrue(true);
    }

    private function createEvent(?string $route, string $path): TerminateEvent
    {
        $request = Request::create($path);
        if ($route !== null) {
            $request->attributes->set('_route', $route);
        }

        return new TerminateEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            new Response(),
        );
    }
}
