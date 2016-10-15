<?php

namespace spec\Sylius\Component\Core\Currency;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Currency\SessionBasedCurrencyStorage;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin SessionBasedCurrencyStorage
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SessionBasedCurrencyStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SessionBasedCurrencyStorage::class);
    }

    function it_is_a_currency_storage()
    {
        $this->shouldImplement(CurrencyStorageInterface::class);
    }

    function it_sets_a_currency_for_a_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->set('_currency_web', 'BTC')->shouldBeCalled();

        $this->set($channel, 'BTC');
    }

    function it_gets_a_currency_for_a_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->get('_currency_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_throws_a_currency_not_found_exception_if_storage_does_not_have_currency_code_for_given_channel(
        SessionInterface $session,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $session->get('_currency_web')->willReturn(null);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('get', [$channel]);
    }
}
