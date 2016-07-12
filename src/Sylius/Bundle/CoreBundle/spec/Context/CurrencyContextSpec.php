<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Context\CurrencyContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * @mixin CurrencyContext
 */
final class CurrencyContextSpec extends ObjectBehavior
{
    function let(StorageInterface $storage, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($storage, $channelContext, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    function it_implements_currency_context_interface()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_gets_default_currency_code()
    {
        $this->getDefaultCurrencyCode()->shouldReturn('EUR');
    }

    function it_gets_currency_code_from_the_storage(
        StorageInterface $storage,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $storage->getData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_gets_currency_code_from_the_storage_even_if_there_is_no_channel(
        StorageInterface $storage,
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $storage->getData(sprintf(CurrencyContext::STORAGE_KEY, '__DEFAULT__'), 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_sets_currency_code_to_the_storage(
        StorageInterface $storage,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $storage->setData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }

    function it_sets_currency_code_to_the_storage_even_if_there_is_no_channel(
        StorageInterface $storage,
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $storage->setData(sprintf(CurrencyContext::STORAGE_KEY, '__DEFAULT__'), 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }
}
