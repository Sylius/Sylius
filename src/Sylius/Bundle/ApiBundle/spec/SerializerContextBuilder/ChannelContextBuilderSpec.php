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

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $channelContext);
    }

    function it_updates_an_context_when_channel_context_has_channel(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, true, [])->shouldBeCalled();
        $channelContext->getChannel()->willReturn($channel);

        $this->createFromRequest($request, true, []);
    }
}
