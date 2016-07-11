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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Context\CurrencyContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

/**
 * @mixin CurrencyContext
 */
class CurrencyContextSpec extends ObjectBehavior
{
    function let(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ObjectManager $customerManager,
        ChannelContextInterface $channelContext
    ) {
        $this->beConstructedWith($storage, $customerContext, $customerManager, $channelContext, 'EUR');
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

    function it_gets_currency_code_from_session_if_there_is_no_customer(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $storage->getData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_gets_currency_code_from_session_if_there_is_no_customer_and_no_channel(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $storage->getData(sprintf(CurrencyContext::STORAGE_KEY, '__DEFAULT__'), 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_gets_currency_code_from_customer(
        CustomerContextInterface $customerContext,
        CustomerInterface $customer
    ) {
        $customerContext->getCustomer()->willReturn($customer);
        $customer->getCurrencyCode()->willReturn('PLN');

        $this->getCurrencyCode()->shouldReturn('PLN');
    }

    function it_sets_currency_code_to_session_if_there_is_no_customer(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $storage->setData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }

    function it_sets_currency_code_to_session_if_there_is_no_customer_and_no_channel(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $storage->setData(sprintf(CurrencyContext::STORAGE_KEY, '__DEFAULT__'), 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }

    function it_sets_currency_code_to_customer(
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CustomerInterface $customer
    ) {
        $customerContext->getCustomer()->willReturn($customer);
        $customer->setCurrencyCode('PLN')->shouldBeCalled();

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $this->setCurrencyCode('PLN');
    }
}
