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

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class FakeChannelCodeProviderSpec extends ObjectBehavior
{
    function it_implements_a_channel_code_provider_interface(): void
    {
        $this->shouldImplement(FakeChannelCodeProviderInterface::class);
    }

    function it_returns_fake_channel_code_from_query_string(): void
    {
        $request = new Request(query: ['_channel_code' => 'channel_code_form_get']);

        $this->getCode($request)->shouldReturn('channel_code_form_get');
    }

    function it_returns_fake_channel_code_from_cookie_if_there_is_none_in_query_string(): void {
        $request = new Request(
            query: ['_channel_code' => null],
            cookies: ['_channel_code' => 'channel_code_form_cookie'],
        );

        $this->getCode($request)->shouldReturn('channel_code_form_cookie');
    }

    function it_returns_null_channel_code_if_no_fake_channel_code_was_found(): void {
        $request = new Request(
            query: ['_channel_code' => null],
            cookies: ['_channel_code' => null],
        );

        $this->getCode($request)->shouldReturn(null);
    }
}
