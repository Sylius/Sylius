<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Channel\Context\RequestBased;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class CompositeRequestResolverSpec extends ObjectBehavior
{
    function it_implements_request_resolver_interface(): void
    {
        $this->shouldImplement(RequestResolverInterface::class);
    }

    function it_returns_null_if_there_are_no_nested_request_resolvers_added(Request $request): void
    {
        $this->findChannel($request)->shouldReturn(null);
    }

    function it_returns_null_if_none_of_nested_request_resolvers_returned_channel(
        Request $request,
        RequestResolverInterface $requestResolver
    ): void {
        $requestResolver->findChannel($request)->willReturn(null);

        $this->addResolver($requestResolver);

        $this->findChannel($request)->shouldReturn(null);
    }

    function it_returns_first_result_returned_by_nested_request_resolvers(
        Request $request,
        RequestResolverInterface $firstRequestResolver,
        RequestResolverInterface $secondRequestResolver,
        RequestResolverInterface $thirdRequestResolver,
        ChannelInterface $channel
    ): void {
        $firstRequestResolver->findChannel($request)->willReturn(null);
        $secondRequestResolver->findChannel($request)->willReturn($channel);
        $thirdRequestResolver->findChannel($request)->shouldNotBeCalled();

        $this->addResolver($firstRequestResolver);
        $this->addResolver($secondRequestResolver);
        $this->addResolver($thirdRequestResolver);

        $this->findChannel($request)->shouldReturn($channel);
    }

    function its_nested_request_resolvers_can_have_priority(
        Request $request,
        RequestResolverInterface $firstRequestResolver,
        RequestResolverInterface $secondRequestResolver,
        RequestResolverInterface $thirdRequestResolver,
        ChannelInterface $channel
    ): void {
        $firstRequestResolver->findChannel($request)->shouldNotBeCalled();
        $secondRequestResolver->findChannel($request)->willReturn($channel);
        $thirdRequestResolver->findChannel($request)->willReturn(null);

        $this->addResolver($firstRequestResolver, -5);
        $this->addResolver($secondRequestResolver, 0);
        $this->addResolver($thirdRequestResolver, 5);

        $this->findChannel($request)->shouldReturn($channel);
    }
}
