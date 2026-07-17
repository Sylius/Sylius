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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersisterTest extends TestCase
{
    /** @var FakeChannelCodeProviderInterface&MockObject */
    private MockObject $fakeChannelCodeProvider;

    private FakeChannelPersister $fakeChannelPersister;

    private HttpKernelInterface&MockObject $kernelMock;

    private Request $request;

    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeChannelCodeProvider = $this->createMock(FakeChannelCodeProviderInterface::class);
        $this->fakeChannelPersister = new FakeChannelPersister($this->fakeChannelCodeProvider);
        $this->kernelMock = $this->createMock(HttpKernelInterface::class);
        $this->request = new Request();
        $this->response = new Response();
    }

    public function testAppliesOnlyToMasterRequests(): void
    {
        $this->fakeChannelCodeProvider->expects(self::never())->method('getCode');

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->request,
            HttpKernelInterface::SUB_REQUEST,
            $this->response,
        ));

        self::assertCount(0, $this->response->headers->getCookies());
    }

    public function testAppliesOnlyForRequestWithFakeChannelCode(): void
    {
        $this->fakeChannelCodeProvider->expects(self::once())
            ->method('getCode')
            ->with($this->request)
            ->willReturn(null);

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->request,
            HttpKernelInterface::MAIN_REQUEST,
            $this->response,
        ));

        self::assertCount(0, $this->response->headers->getCookies());
    }

    public function testPersistsFakeChannelCodesInACookie(): void
    {
        $this->fakeChannelCodeProvider
            ->expects(self::once())
            ->method('getCode')
            ->with($this->request)
            ->willReturn('fake_channel_code');

        $this->fakeChannelPersister->onKernelResponse(new ResponseEvent(
            $this->kernelMock,
            $this->request,
            HttpKernelInterface::MAIN_REQUEST,
            $this->response,
        ));

        $cookies = $this->response->headers->getCookies();

        self::assertCount(1, $cookies);
        self::assertSame('_channel_code', $cookies[0]->getName());
        self::assertSame('fake_channel_code', $cookies[0]->getValue());
    }
}
