<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Updater\OrderExchangeRateAndCurrencyUpdater;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin OrderExchangeRateAndCurrencyUpdater
 *
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderExchangeRateAndCurrencyUpdaterSpec extends ObjectBehavior
{
    function let(
        CurrencyContextInterface $currencyContext,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $cartRepository
    ) {
        $this->beConstructedWith($currencyContext, $currencyRepository, $cartRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderExchangeRateAndCurrencyUpdater::class);
    }

    function it_implements_order_exchange_rate_and_currency_updater_interface()
    {
        $this->shouldImplement(OrderUpdaterInterface::class);
    }

    function it_updates_order_exchange_rate(
        OrderInterface $order,
        CurrencyContextInterface $currencyContext,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $cartRepository,
        CurrencyInterface $currency,
        ChannelInterface $channel
    ) {
        $order->getChannel()->willReturn($channel);
        $currencyContext->getCurrencyCode()->willReturn('GBP');
        $currencyRepository->findOneBy(['code' => 'GBP'])->willReturn($currency);
        $currency->getExchangeRate()->willReturn(3.5);
        $currency->getCode()->willReturn('GBP');

        $order->setCurrencyCode('GBP')->shouldBeCalled();
        $order->setExchangeRate(3.5)->shouldBeCalled();

        $cartRepository->add($order)->shouldBeCalled();

        $this->update($order);
    }
}
