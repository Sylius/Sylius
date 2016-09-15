<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Handler\ShopCurrencyChangeHandler;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\SyliusCurrencyEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin ShopCurrencyChangeHandler
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ShopCurrencyChangeHandlerSpec extends ObjectBehavior
{
    function let(
        CurrencyStorageInterface $currencyStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($currencyStorage, $channelContext, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopCurrencyChangeHandler::class);
    }

    function it_implements_currency_change_handler_interface()
    {
        $this->shouldImplement(CurrencyChangeHandlerInterface::class);
    }

    function it_throws_handle_exception_when_a_channel_is_not_found(ChannelContextInterface $channelContext)
    {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(HandleException::class)->during('handle', [Argument::any()]);
    }

    function it_handles_shop_currency_code_change(
        CurrencyStorageInterface $currencyStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $currencyStorage->set($channel, 'USD')->shouldBeCalled();

        $eventDispatcher->dispatch(SyliusCurrencyEvents::CODE_CHANGED, Argument::type(GenericEvent::class))->shouldBeCalled();

        $this->handle('USD');
    }
}
