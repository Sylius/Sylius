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

namespace Tests\Sylius\Bundle\AdminBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\AdminFilterSubscriber;
use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AdminFilterSubscriberTest extends TestCase
{
    private FilterStorageInterface&MockObject $filterStorage;

    private AdminFilterSubscriber $adminFilterSubscriber;

    protected function setUp(): void
    {
        $this->filterStorage = $this->createMock(FilterStorageInterface::class);
        $this->adminFilterSubscriber = new AdminFilterSubscriber($this->filterStorage);
    }

    public function testSubscribesToKernelRequestEvent(): void
    {
        $this->assertSame([KernelEvents::REQUEST => 'onKernelRequest'], AdminFilterSubscriber::getSubscribedEvents());
    }

    public function testAddsFilterToFilterStorage(): void
    {
        $filterStorage = $this->createMock(FilterStorageInterface::class);
        $subscriber = new AdminFilterSubscriber($filterStorage);

        $request = new Request(query: ['filter' => 'foo']);
        $request->attributes = new ParameterBag([
            '_route' => 'sylius_admin_product_index',
            '_sylius' => ['section' => 'admin'],
            '_controller' => 'Sylius\\Bundle\\AdminBundle\\Controller\\ProductController::indexAction',
        ]);

        $event = $this->createMock(RequestEvent::class);
        $event->method('isMainRequest')->willReturn(true);
        $event->method('getRequest')->willReturn($request);

        $filterStorage->expects($this->once())->method('all')->willReturn([]);
        $filterStorage->expects($this->once())->method('set');

        $subscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfNotMainRequest(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->method('isMainRequest')->willReturn(false);

        $event->expects($this->never())->method('getRequest');

        $this->adminFilterSubscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfRequestFormatIsNotHtml(): void
    {
        $filterStorage = $this->createMock(FilterStorageInterface::class);
        $this->adminFilterSubscriber = new AdminFilterSubscriber($filterStorage);

        $request = $this->createMock(Request::class);
        $attributes = $this->createMock(ParameterBag::class);
        $request->attributes = $attributes;

        $request->method('getRequestFormat')->willReturn('json');
        $request->query = new InputBag(['filter' => 'foo']);

        $attributes->method('get')
            ->willReturnMap([
                ['_route', '', 'sylius_admin_product_index'],
                ['_sylius', [], ['section' => 'admin']],
                ['_controller', null, 'Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction'],
            ]);

        $filterStorage->expects($this->never())->method('set');

        $event = $this->createMock(RequestEvent::class);
        $event->method('isMainRequest')->willReturn(true);
        $event->method('getRequest')->willReturn($request);

        $this->adminFilterSubscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfNotAdminSection(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $attributes = $this->createMock(ParameterBag::class);

        $event->method('isMainRequest')->willReturn(true);
        $request->method('getRequestFormat')->willReturn('html');

        $attributes->method('get')->willReturnMap([
            ['_route', '', 'sylius_admin_product_index'],
            ['_sylius', [], ['section' => 'shop']],
            ['_controller', null, 'Sylius\\Bundle\\AdminBundle\\Controller\\ProductController::indexAction'],
        ]);

        $request->attributes = $attributes;
        $request->query = new InputBag(['filter' => 'foo']);

        $event->method('getRequest')->willReturn($request);

        $this->filterStorage->expects($this->never())->method('set');

        $this->adminFilterSubscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfControllerIsNull(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $attributes = $this->createMock(ParameterBag::class);

        $event->method('isMainRequest')->willReturn(true);
        $request->method('getRequestFormat')->willReturn('html');

        $attributes->method('get')->willReturnMap([
            ['_route', '', 'sylius_admin_product_index'],
            ['_sylius', [], ['section' => 'admin']],
            ['_controller', null, null],
        ]);

        $request->attributes = $attributes;
        $request->query = new InputBag(['filter' => 'foo']);

        $event->method('getRequest')->willReturn($request);

        $this->filterStorage->expects($this->never())->method('set');

        $this->adminFilterSubscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfRouteIsMissing(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $attributes = $this->createMock(ParameterBag::class);

        $event->method('isMainRequest')->willReturn(true);
        $request->method('getRequestFormat')->willReturn('html');

        $attributes->method('get')->willReturnMap([
            ['_route', '', ''],
            ['_sylius', [], ['section' => 'admin']],
            ['_controller', null, 'Sylius\\Bundle\\AdminBundle\\Controller\\ProductController::indexAction'],
        ]);

        $request->attributes = $attributes;
        $request->query = new InputBag(['filter' => 'foo']);

        $event->method('getRequest')->willReturn($request);

        $this->filterStorage->expects($this->never())->method('set');

        $this->adminFilterSubscriber->onKernelRequest($event);
    }

    public function testDoesNotAddFilterIfRouteIsNotIndex(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $attributes = $this->createMock(ParameterBag::class);

        $event->method('isMainRequest')->willReturn(true);
        $request->method('getRequestFormat')->willReturn('html');

        $attributes->method('get')->willReturnMap([
            ['_route', '', 'sylius_admin_product_update'],
            ['_sylius', [], ['section' => 'admin']],
            ['_controller', null, 'Sylius\\Bundle\\AdminBundle\\Controller\\ProductController::indexAction'],
        ]);

        $request->attributes = $attributes;
        $request->query = new InputBag(['filter' => 'foo']);

        $event->method('getRequest')->willReturn($request);

        $this->filterStorage->expects($this->never())->method('set');

        $this->adminFilterSubscriber->onKernelRequest($event);
    }
}
