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
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class FakeChannelContextTest extends TestCase
{
    /** @var FakeChannelCodeProviderInterface&MockObject */
    private MockObject $fakeChannelCodeProvider;

    /** @var ChannelRepositoryInterface&MockObject */
    private MockObject $channelRepository;

    /** @var RequestStack&MockObject */
    private MockObject $requestStack;

    private FakeChannelContext $fakeChannelContext;

    /** @var Request&MockObject */
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeChannelCodeProvider = $this->createMock(FakeChannelCodeProviderInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->fakeChannelContext = new FakeChannelContext(
            $this->fakeChannelCodeProvider,
            $this->channelRepository,
            $this->requestStack,
        );
        $this->request = $this->createMock(Request::class);
    }

    public function testImplementsChannelContextInterface(): void
    {
        self::assertInstanceOf(ChannelContextInterface::class, $this->fakeChannelContext);
    }

    public function testReturnsAFakeChannelWithTheGivenCode(): void
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($this->request);

        $this->fakeChannelCodeProvider->expects(self::once())
            ->method('getCode')
            ->with($this->request)
            ->willReturn('CHANNEL_CODE');

        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('CHANNEL_CODE')
            ->willReturn($channel);

        self::assertSame($channel, $this->fakeChannelContext->getChannel());
    }

    public function testThrowsAChannelNotFoundExceptionIfThereIsNoMasterRequest(): void
    {
        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn(null);

        self::expectException(ChannelNotFoundException::class);

        $this->fakeChannelContext->getChannel();
    }

    public function testThrowsAChannelNotFoundExceptionIfProvidedCodeWasNull(): void
    {
        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($this->request);

        $this->fakeChannelCodeProvider->expects(self::once())
            ->method('getCode')
            ->with($this->request)
            ->willReturn(null);

        $this->channelRepository->expects(self::never())->method('findOneByCode');

        self::expectException(ChannelNotFoundException::class);

        $this->fakeChannelContext->getChannel();
    }

    public function testThrowsAChannelNotFoundExceptionIfChannelWithGivenCodeWasNotFound(): void
    {
        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($this->request);

        $this->fakeChannelCodeProvider->expects(self::once())
            ->method('getCode')
            ->with($this->request)
            ->willReturn('CHANNEL_CODE');

        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('CHANNEL_CODE')
            ->willReturn(null);

        self::expectException(ChannelNotFoundException::class);

        $this->fakeChannelContext->getChannel();
    }
}
