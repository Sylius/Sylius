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
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContext as SyliusCurrencyContext;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

class CurrencyContextSpec extends ObjectBehavior
{
    function let(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $customerManager,
        SettingsInterface $settings,
        ChannelContextInterface $channelContext
    ) {
        $settingsManager->load('sylius_general')->willReturn($settings);
        $settings->get('currency')->willReturn('EUR');

        $this->beConstructedWith($storage, $customerContext, $settingsManager, $customerManager, $channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    function it_extends_Sylius_currency_context()
    {
        $this->shouldHaveType(SyliusCurrencyContext::class);
    }

    function it_gets_default_currency_code()
    {
        $this->getDefaultCurrencyCode()->shouldReturn('EUR');
    }

    function it_gets_currency_code_from_session_if_there_is_no_customer(
        $customerContext,
        $storage,
        ChannelInterface $channel,
        $channelContext
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $storage->getData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_gets_currency_code_from_customer(
        CustomerInterface $customer,
        $customerContext
    ) {
        $customerContext->getCustomer()->willReturn($customer);
        $customer->getCurrencyCode()->willReturn('PLN');

        $this->getCurrencyCode()->shouldReturn('PLN');
    }

    function it_sets_currency_code_to_session_if_there_is_no_customer(
        $customerContext,
        $storage,
        ChannelInterface $channel,
        $channelContext
    ) {
        $customerContext->getCustomer()->willReturn(null);

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $storage->setData(sprintf(CurrencyContext::STORAGE_KEY, 'WEB'), 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }

    function it_sets_currency_code_to_customer(
        CustomerInterface $customer,
        $customerContext,
        ChannelInterface $channel,
        $channelContext
    ) {
        $customerContext->getCustomer()->willReturn($customer);
        $customer->setCurrencyCode('PLN')->shouldBeCalled();

        $channel->getCode()->willReturn('WEB');
        $channelContext->getChannel()->willReturn($channel);

        $this->setCurrencyCode('PLN');
    }
}
