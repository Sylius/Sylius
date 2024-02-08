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

namespace spec\Sylius\Component\Core\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ShopperContextSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CustomerContextInterface $customerContext,
    ): void {
        $this->beConstructedWith($channelContext, $currencyContext, $localeContext, $customerContext);
    }

    function it_implements_a_shopper_context_interface(): void
    {
        $this->shouldImplement(ShopperContextInterface::class);
    }

    function it_gets_a_current_channel_from_a_context(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_gets_a_current_currency_code_from_a_context(CurrencyContextInterface $currencyContext): void
    {
        $currencyContext->getCurrencyCode()->willReturn('USD');

        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_gets_a_current_locale_code_from_a_context(LocaleContextInterface $localeContext): void
    {
        $localeContext->getLocaleCode()->willReturn('en_US');

        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_gets_a_current_customer_from_a_context(
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
    ): void {
        $customerContext->getCustomer()->willReturn($customer);

        $this->getCustomer()->shouldReturn($customer);
    }
}
