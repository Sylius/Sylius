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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteExceptionListener;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResourceDeleteExceptionListenerTest extends TestCase
{
    private MockObject&UrlGeneratorInterface $router;

    private MockObject&RequestStack $requestStack;

    private ResourceDeleteExceptionListener $listener;

    protected function setUp(): void
    {
        $this->router = $this->createMock(UrlGeneratorInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->listener = new ResourceDeleteExceptionListener($this->router, $this->requestStack);
    }

    public function testDoesNothingIfExceptionIsNotResourceDeleteException(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();

        $exception = $this->createMock(ForeignKeyConstraintViolationException::class);

        $event = new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $this->listener->onResourceDeleteException($event);

        $this->assertNull($event->getResponse());
    }

    public function testDoesNothingIfRequestComesFromApi(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->attributes = new ParameterBag(['_api_operation' => 'sylius_api_admin_product_delete']);
        $request->headers = new HeaderBag(['referer' => '/admin/product/index']);

        $exception = new ResourceDeleteException('Product');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->listener->onResourceDeleteException($event);

        $this->assertNull($event->getResponse());
    }

    public function testRedirectsToRefererIfPresentAndAddsFlashMessage(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->attributes = new ParameterBag(['_route' => 'sylius_admin_product_delete']);
        $request->headers = new HeaderBag(['referer' => '/admin/product/index']);

        $exception = new ResourceDeleteException('Product');

        $session = $this->createMock(SessionInterface::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        $session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashBag);

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'Product'],
            ])
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($session);
        $this->router->expects($this->never())->method('generate');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->listener->onResourceDeleteException($event);

        $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());
        $this->assertSame('/admin/product/index', $event->getResponse()->getTargetUrl());
    }

    public function testRedirectsToIndexWhenNoRefererPresentAndAddsFlashMessage(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->attributes = new ParameterBag(['_route' => 'sylius_admin_product_delete']);
        $request->headers = new HeaderBag();

        $exception = new ResourceDeleteException('Product');

        $session = $this->createMock(SessionInterface::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        $session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashBag);

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'Product'],
            ])
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($session);

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_admin_product_index')
            ->willReturn('/admin/product/index')
        ;

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->listener->onResourceDeleteException($event);

        $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());
        $this->assertSame('/admin/product/index', $event->getResponse()->getTargetUrl());
    }
}
