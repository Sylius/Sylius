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

namespace Tests\Sylius\Bundle\ApiBundle\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class KernelRequestEventSubscriberTest extends TestCase
{
    private KernelRequestEventSubscriber $kernelRequestEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernelRequestEventSubscriber = new KernelRequestEventSubscriber(true, '/api/v2');
    }

    public function testDoesNothingIfApiIsEnabled(): void
    {
        /** @var RequestEvent|MockObject $eventMock */
        $eventMock = $this->createMock(RequestEvent::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $eventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->expects(self::once())->method('getPathInfo')->willReturn('/api/v2/any-endpoint');

        $this->kernelRequestEventSubscriber->validateApi(new RequestEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }

    public function testThrowsNotFoundExceptionIfApiIsDisabled(): void
    {
        /** @var RequestEvent|MockObject $eventMock */
        $eventMock = $this->createMock(RequestEvent::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $this->kernelRequestEventSubscriber = new KernelRequestEventSubscriber(false, '/api/v2');

        $eventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->expects(self::once())->method('getPathInfo')->willReturn('/api/v2/any-endpoint');

        self::expectException(NotFoundHttpException::class);

        $this->kernelRequestEventSubscriber->validateApi(new RequestEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }

    public function testDoesNothingForNonApiEndpointsWhenApiIsDisabled(): void
    {
        /** @var RequestEvent|MockObject $eventMock */
        $eventMock = $this->createMock(RequestEvent::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $this->kernelRequestEventSubscriber = new KernelRequestEventSubscriber(false, '/api/v2');

        $eventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->expects(self::once())->method('getPathInfo')->willReturn('/');

        $this->kernelRequestEventSubscriber->validateApi(new RequestEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }
}
