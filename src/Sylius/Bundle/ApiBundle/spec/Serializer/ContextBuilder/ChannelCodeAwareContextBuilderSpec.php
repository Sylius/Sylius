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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ChannelCodeAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith(
            $decoratedContextBuilder,
            ChannelCodeAware::class,
            'channelCode',
            $channelContext,
        );
    }

    function it_sets_channel_code_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('CODE');

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn([
                'input' => ['class' => SendContactRequest::class],
                AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                    SendContactRequest::class => ['channelCode' => 'CODE'],
                ],
            ])
        ;
    }

    function it_does_nothing_if_there_is_no_input_class(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn([])
        ;

        $channelContext->getChannel()->shouldNotBeCalled();

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_nothing_if_input_class_is_no_channel_aware(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $channelContext->getChannel()->shouldNotBeCalled();

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }
}
