<?php

namespace spec\Sylius\Component\Core\Locale;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\SessionBasedLocaleStorage;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin SessionBasedLocaleStorage
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SessionBasedLocaleStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SessionBasedLocaleStorage::class);
    }

    function it_is_a_locale_storage()
    {
        $this->shouldImplement(LocaleStorageInterface::class);
    }

    function it_sets_a_locale_for_a_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->set('_locale_web', 'BTC')->shouldBeCalled();

        $this->set($channel, 'BTC');
    }

    function it_gets_a_locale_for_a_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->get('_locale_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_throws_a_locale_not_found_exception_if_storage_does_not_have_locale_code_for_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->get('_locale_web')->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('get', [$channel]);
    }
}
