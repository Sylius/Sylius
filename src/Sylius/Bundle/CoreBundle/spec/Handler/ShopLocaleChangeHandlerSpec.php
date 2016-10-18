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
use Sylius\Bundle\CoreBundle\Handler\ShopLocaleChangeHandler;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\SyliusLocaleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopLocaleChangeHandlerSpec extends ObjectBehavior
{
    function let(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($localeStorage, $channelContext, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopLocaleChangeHandler::class);
    }

    function it_implements_locale_change_handler_interface()
    {
        $this->shouldImplement(LocaleChangeHandlerInterface::class);
    }

    function it_handles_locale_change(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        Channel $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $localeStorage->set($channel, 'en_GB')->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent('en_GB'))->shouldBeCalled();

        $this->handle('en_GB');
    }

    function it_throws_handle_exception_if_channel_was_not_found(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $localeStorage->set(Argument::any(), Argument::any())->shouldNotBeCalled();
        $eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent('en_GB'))->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', ['en_GB']);
    }
}
