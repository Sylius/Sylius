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

namespace Tests\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelPersister;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersisterTest extends TestCase
{
    /** @var FakeChannelCodeProviderInterface&MockObject */
    private MockObject $fakeChannelCodeProvider;

    private FakeChannelPersister $fakeChannelPersister;

    private HttpKernelInterface&MockObject $kernelMock;

    private MockObject&Request $requestMock;

    private MockObject&Response $responseMock;

    private MockObject&ResponseHeaderBag $responseHeaderBag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeChannelCodeProvider = $this->createMock(FakeChannelCodeProviderInterface::class);
        $this->fakeChannelPersister = new FakeChannelPersister($this->fakeChannelCodeProvider);
        $this->kernelMock = $this->createMock(HttpKernelInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->responseMock = $this->createMock(Response::class);
        $this->responseHeaderBag = $this->createMock(ResponseHeaderBag::class);
    }

    public function testAppliesOnlyToMasterRequests(): void
    {
        $this->fakeChannelCodeProvider->expects(self::never())->method('getCode');

        $this->responseMock->headers = $this->responseHeaderBag;

        $this->responseMock->headers->expects(self::never())->method('setCookie');

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->requestMock,
            HttpKernelInterface::SUB_REQUEST,
            $this->responseMock,
        ));
    }

    public function testAppliesOnlyForRequestWithFakeChannelCode(): void
    {
        $this->fakeChannelCodeProvider->expects(self::once())
            ->method('getCode')
            ->with($this->requestMock)
            ->willReturn(null);

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $this->responseMock,
        ));
    }

    public function testPersistsFakeChannelCodesInACookie(): void
    {
        $this->fakeChannelCodeProvider
            ->expects(self::once())
            ->method('getCode')
            ->with($this->requestMock)
            ->willReturn('fake_channel_code');

        $this->responseMock->headers = $this->responseHeaderBag;

        $this->responseHeaderBag
            ->expects(self::once())
            ->method('setCookie')
            ->with($this->callback(fn (Cookie $cookie) => $cookie->getName() === '_channel_code' &&
                $cookie->getValue() === 'fake_channel_code'));

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $this->responseMock,
        ));
    }
}
