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

namespace Tests\Sylius\Bundle\AdminBundle\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Controller\RedirectHandler;
use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;

final class RedirectHandlerTest extends TestCase
{
    private MockObject&RedirectHandlerInterface $decoratedRedirectHandler;

    private FilterStorageInterface&MockObject $filterStorage;

    private RedirectHandler $redirectHandler;

    protected function setUp(): void
    {
        $this->decoratedRedirectHandler = $this->createMock(RedirectHandlerInterface::class);
        $this->filterStorage = $this->createMock(FilterStorageInterface::class);

        $this->redirectHandler = new RedirectHandler(
            $this->decoratedRedirectHandler,
            $this->filterStorage,
        );
    }

    public function testImplementsRedirectHandlerInterface(): void
    {
        $this->assertInstanceOf(RedirectHandlerInterface::class, $this->redirectHandler);
    }

    public function testRedirectsToResource(): void
    {
        $configuration = $this->createMock(RequestConfiguration::class);
        $resource = $this->createMock(ResourceInterface::class);
        $expectedResponse = new Response();

        $this->decoratedRedirectHandler
            ->expects($this->once())
            ->method('redirectToResource')
            ->with($configuration, $resource)
            ->willReturn($expectedResponse)
        ;

        $actualResponse = $this->redirectHandler->redirectToResource($configuration, $resource);

        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testRedirectsToIndex(): void
    {
        $configuration = $this->createMock(RequestConfiguration::class);
        $resource = $this->createMock(ResourceInterface::class);

        $this->filterStorage->method('all')->willReturn(['foo' => 'bar']);

        $configuration->method('getRedirectRoute')->with('index')->willReturn('index');
        $configuration->method('getRedirectParameters')->with($resource)->willReturn([]);
        $expectedResponse = new Response();

        $this->decoratedRedirectHandler
            ->expects($this->once())
            ->method('redirectToRoute')
            ->with($configuration, 'index', ['foo' => 'bar'])
            ->willReturn($expectedResponse)
        ;

        $actualResponse = $this->redirectHandler->redirectToRoute($configuration, 'index', ['foo' => 'bar']);

        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testRedirects(): void
    {
        $configuration = $this->createMock(RequestConfiguration::class);
        $expectedResponse = new Response();

        $this->decoratedRedirectHandler
            ->expects($this->once())
            ->method('redirect')
            ->with($configuration, 'http://test.com', 302)
            ->willReturn($expectedResponse)
        ;

        $actualResponse = $this->redirectHandler->redirect($configuration, 'http://test.com', 302);

        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testRedirectsToRoute(): void
    {
        $configuration = $this->createMock(RequestConfiguration::class);
        $expectedResponse = new Response();

        $this->decoratedRedirectHandler
            ->expects($this->once())
            ->method('redirectToRoute')
            ->with($configuration, 'my_route', [])
            ->willReturn($expectedResponse)
        ;

        $actualResponse = $this->redirectHandler->redirectToRoute($configuration, 'my_route', []);

        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testRedirectsToReferer(): void
    {
        $configuration = $this->createMock(RequestConfiguration::class);
        $expectedResponse = new Response();

        $this->decoratedRedirectHandler
            ->expects($this->once())
            ->method('redirectToReferer')
            ->with($configuration)
            ->willReturn($expectedResponse)
        ;

        $actualResponse = $this->redirectHandler->redirectToReferer($configuration);

        $this->assertSame($expectedResponse, $actualResponse);
    }
}
