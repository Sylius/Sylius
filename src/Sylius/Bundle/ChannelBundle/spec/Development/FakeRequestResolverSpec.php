<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ChannelBundle\Development;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ChannelBundle\Development\FakeHostnameProviderInterface;
use Sylius\Bundle\ChannelBundle\Development\FakeRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @mixin FakeRequestResolver
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FakeRequestResolverSpec extends ObjectBehavior
{
    function let(RequestResolverInterface $decoratedRequestResolver, FakeHostnameProviderInterface $fakeHostnameProvider)
    {
        $this->beConstructedWith($decoratedRequestResolver, $fakeHostnameProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ChannelBundle\Development\FakeRequestResolver');
    }

    function it_implements_request_resolver_interface()
    {
        $this->shouldImplement(RequestResolverInterface::class);
    }

    function it_proxies_decorated_request_resolver_if_there_is_no_fake_hostname_found(
        RequestResolverInterface $decoratedRequestResolver,
        FakeHostnameProviderInterface $fakeHostnameProvider,
        Request $request,
        ChannelInterface $channel
    ) {
        $fakeHostnameProvider->getHostname($request)->willReturn(null);

        $decoratedRequestResolver->findChannel($request)->willReturn($channel);

        $this->findChannel($request)->shouldReturn($channel);
    }

    function it_proxies_decorated_request_resolver_with_cloned_request_if_fake_hostname_was_found(
        RequestResolverInterface $decoratedRequestResolver,
        FakeHostnameProviderInterface $fakeHostnameProvider,
        ChannelInterface $channel
    ) {
        $request = new Request();

        $fakeHostnameProvider->getHostname($request)->willReturn('fake.hostname');

        $decoratedRequestResolver->findChannel(Argument::that(function (Request $clonedRequest) use ($request) {
            if ($request === $clonedRequest) {
                return false;
            }

            if ('fake.hostname' !== $clonedRequest->headers->get('HOST')) {
                return false;
            }

            return true;
        }))->willReturn($channel);

        $this->findChannel($request)->shouldReturn($channel);
    }
}
