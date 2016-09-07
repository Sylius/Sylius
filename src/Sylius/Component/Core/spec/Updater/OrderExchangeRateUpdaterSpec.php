<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Updater;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Updater\OrderExchangeRateUpdater;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin OrderExchangeRateUpdater
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderExchangeRateUpdaterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderExchangeRateUpdater::class);
    }

    function it_implements_order_updater_interface()
    {
        $this->shouldImplement(OrderUpdaterInterface::class);
    }

    function it_throws_exception_when_currency_from_order_was_not_found_in_the_system(
        RepositoryInterface $currencyRepository,
        OrderInterface $order
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $currencyRepository->findOneBy(['code' => 'USD'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('update', [$order]);
    }

    function it_updates_orders_exchange_rate(
        RepositoryInterface $currencyRepository,
        OrderInterface $order,
        CurrencyInterface $currency
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $currency->getCode()->willReturn('USD');
        $currency->getExchangeRate()->willReturn(2);

        $currencyRepository->findOneBy(['code' => 'USD'])->willReturn($currency);

        $order->setExchangeRate(2)->shouldBeCalled();

        $this->update($order);
    }
}
