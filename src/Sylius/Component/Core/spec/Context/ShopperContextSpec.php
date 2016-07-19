<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Context\ShopperContext;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

/**
 * @mixin ShopperContext
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShopperContextSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CustomerContextInterface $customerContext
    ) {
        $this->beConstructedWith($channelContext, $currencyContext, $localeContext, $customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Context\ShopperContext');
    }

    function it_implements_a_shopper_context_interface()
    {
        $this->shouldImplement(ShopperContextInterface::class);
    }

    function it_gets_current_channel_from_context(ChannelContextInterface $channelContext, ChannelInterface $channel)
    {
        $channelContext->getChannel()->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_gets_current_currency_code_from_context(CurrencyContextInterface $currencyContext)
    {
        $currencyContext->getCurrencyCode()->willReturn('USD');

        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_gets_current_locale_code_from_context(LocaleContextInterface $localeContext)
    {
        $localeContext->getCurrentLocale()->willReturn('en_US');

        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_gets_current_customer_from_context(CustomerContextInterface $customerContext, CustomerInterface $customer)
    {
        $customerContext->getCustomer()->willReturn($customer);

        $this->getCustomer()->shouldReturn($customer);
    }
}
