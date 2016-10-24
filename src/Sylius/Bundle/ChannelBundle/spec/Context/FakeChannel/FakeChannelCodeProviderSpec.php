<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProvider;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FakeChannelCodeProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FakeChannelCodeProvider::class);
    }

    function it_implements_a_channel_code_provider_interface()
    {
        $this->shouldImplement(FakeChannelCodeProviderInterface::class);
    }

    function it_returns_fake_channel_code_from_query_string(Request $request, ParameterBag $queryBag)
    {
        $queryBag->get('_channel_code')->willReturn('channel_code_form_get');
        $request->query = $queryBag;

        $this->getCode($request)->shouldReturn('channel_code_form_get');
    }

    function it_returns_fake_channel_code_from_cookie_if_there_is_none_in_query_string(
        Request $request,
        ParameterBag $queryBag,
        ParameterBag $cookiesBag
    ) {
        $queryBag->get('_channel_code')->willReturn(null);
        $request->query = $queryBag;

        $cookiesBag->get('_channel_code')->willReturn('channel_code_form_cookie');
        $request->cookies = $cookiesBag;

        $this->getCode($request)->shouldReturn('channel_code_form_cookie');
    }

    function it_returns_null_channel_code_if_no_fake_channel_code_was_found(
        Request $request,
        ParameterBag $queryBag,
        ParameterBag $cookiesBag
    ) {
        $queryBag->get('_channel_code')->willReturn(null);
        $request->query = $queryBag;

        $cookiesBag->get('_channel_code')->willReturn(null);
        $request->cookies = $cookiesBag;

        $this->getCode($request)->shouldReturn(null);
    }
}
