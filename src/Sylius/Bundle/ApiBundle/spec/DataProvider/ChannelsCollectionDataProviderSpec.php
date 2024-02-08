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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelsCollectionDataProviderSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($channelContext);
    }

    function it_supports_channel_interface_and_only_shop_context(): void
    {
        $this
            ->supports(
                ChannelInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'shop_get',
                ],
            )
            ->shouldReturn(true)
        ;

        $this
            ->supports(
                ChannelInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'admin_get',
                ],
            )
            ->shouldReturn(false)
        ;

        $this
            ->supports(
                ProductInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'shop_get',
                ],
            )
            ->shouldReturn(false)
        ;
    }

    function it_returns_channel_collection(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $this
            ->getCollection(
                ChannelInterface::class,
                Request::METHOD_GET,
                [],
            )
            ->shouldReturn([$channel])
        ;
    }
}
