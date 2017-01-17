<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Service\Setter\ChannelContextSetter;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContextSetterSpec extends ObjectBehavior
{
    function let(CookieSetterInterface $cookieSetter)
    {
        $this->beConstructedWith($cookieSetter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelContextSetter::class);
    }

    function it_implements_channel_context_setter_interface()
    {
        $this->shouldImplement(ChannelContextSetterInterface::class);
    }

    function it_sets_channel_as_current(
        CookieSetterInterface $cookieSetter,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('CHANNEL_CODE');

        $cookieSetter->setCookie('_channel_code', 'CHANNEL_CODE')->shouldBeCalled();

        $this->setChannel($channel);
    }
}
