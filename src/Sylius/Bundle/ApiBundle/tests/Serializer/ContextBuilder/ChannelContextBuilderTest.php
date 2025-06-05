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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\ChannelContextBuilder;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelContextBuilderTest extends TestCase
{
    private MockObject&SerializerContextBuilderInterface $decoratedContextBuilder;

    private ChannelContextInterface&MockObject $channelContext;

    private ChannelContextBuilder $channelContextBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedContextBuilder = $this->createMock(SerializerContextBuilderInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->channelContextBuilder = new ChannelContextBuilder($this->decoratedContextBuilder, $this->channelContext);
    }

    public function testUpdatesAnContextWhenChannelContextHasChannel(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, []);

        $this->channelContext->expects(self::once())->method('getChannel')->willReturn($channelMock);

        $this->channelContextBuilder->createFromRequest($requestMock, true, []);
    }
}
