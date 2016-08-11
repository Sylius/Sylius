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

use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use PhpSpec\ObjectBehavior; 
use Prophecy\Argument;
use Sylius\Component\Core\OrderProcessing\OrderExchngeRateAndCurrencyUpdaterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderExchangeRateAndCurrencyUpdaterSpec extends ObjectBehavior
{
    function let(CurrencyStorageInterface $currencyStorage, RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyStorage, $currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderExchangeRateAndCurrencyUpdater');
    }

    function it_implements_order_exchange_rate_and_currency_updater_interface()
    {
        $this->shouldImplement(OrderExchngeRateAndCurrencyUpdaterInterface::class);
    }

    function it_updates_order_exchange_rate(
        OrderInterface $order,
        CurrencyStorageInterface $currencyStorage,
        RepositoryInterface $currencyRepository,
        CurrencyInterface $currency,
        ChannelInterface $channel
    ) {
        $order->getChannel()->willReturn($channel);
        $currencyStorage->get($channel)->willReturn('GBP');
        $currencyRepository->findOneBy(['code' => 'GBP'])->willReturn($currency);
        $currency->getExchangeRate()->willReturn(3.5);
        $currency->getCode()->willReturn('GBP');

        $order->setCurrencyCode('GBP')->shouldBeCalled();
        $order->setExchangeRate(3.5)->shouldBeCalled();

        $this->updateExchangeRateAndCurrency($order);
    }
}
