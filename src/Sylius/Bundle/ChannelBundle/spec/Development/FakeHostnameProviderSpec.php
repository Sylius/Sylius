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
use Sylius\Bundle\ChannelBundle\Development\FakeFakeHostnameProvider;
use Sylius\Bundle\ChannelBundle\Development\FakeHostnameProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin FakeFakeHostnameProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FakeHostnameProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ChannelBundle\Development\FakeHostnameProvider');
    }

    function it_implements_hostname_provider_interface()
    {
        $this->shouldImplement(FakeHostnameProviderInterface::class);
    }

    function it_returns_fake_hostname_from_query_string(Request $request, ParameterBag $queryBag)
    {
        $queryBag->get('_hostname')->willReturn('fake.hostname.from.get');
        $request->query = $queryBag;

        $this->getHostname($request)->shouldReturn('fake.hostname.from.get');
    }

    function it_returns_fake_hostname_from_cookie_if_there_is_none_in_query_string(
        Request $request,
        ParameterBag $queryBag,
        ParameterBag $cookiesBag
    ) {
        $queryBag->get('_hostname')->willReturn(null);
        $request->query = $queryBag;

        $cookiesBag->get('_hostname')->willReturn('fake.hostname.from.cookie');
        $request->cookies = $cookiesBag;

        $this->getHostname($request)->shouldReturn('fake.hostname.from.cookie');
    }

    function it_returns_null_hostname_if_no_fake_hostname_was_found(
        Request $request,
        ParameterBag $queryBag,
        ParameterBag $cookiesBag
    ) {
        $queryBag->get('_hostname')->willReturn(null);
        $request->query = $queryBag;

        $cookiesBag->get('_hostname')->willReturn(null);
        $request->cookies = $cookiesBag;

        $this->getHostname($request)->shouldReturn(null);
    }
}
