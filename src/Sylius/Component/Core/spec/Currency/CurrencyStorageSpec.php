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

namespace spec\Sylius\Component\Core\Currency;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Resource\Storage\StorageInterface;

final class CurrencyStorageSpec extends ObjectBehavior
{
    function let(StorageInterface $storage): void
    {
        $this->beConstructedWith($storage);
    }

    function it_is_a_currency_storage(): void
    {
        $this->shouldImplement(CurrencyStorageInterface::class);
    }

    function it_gets_a_currency_for_a_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel,
    ): void {
        $channel->getCode()->willReturn('web');

        $storage->get('_currency_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_sets_a_currency_for_a_given_channel_if_it_is_one_of_the_available_ones_but_not_the_base_one(
        StorageInterface $storage,
        ChannelInterface $channel,
    ): void {
        $usd = new Currency();
        $usd->setCode('USD');

        $eur = new Currency();
        $eur->setCode('EUR');

        $channel->getBaseCurrency()->willReturn($usd);
        $channel->getCurrencies()->willReturn(new ArrayCollection([$usd, $eur]));
        $channel->getCode()->willReturn('web');

        $storage->set('_currency_web', 'EUR')->shouldBeCalled();

        $this->set($channel, 'EUR');
    }

    function it_removes_a_currency_for_a_given_channel_if_it_is_the_base_one(
        StorageInterface $storage,
        ChannelInterface $channel,
    ): void {
        $usd = new Currency();
        $usd->setCode('USD');

        $eur = new Currency();
        $eur->setCode('EUR');

        $channel->getBaseCurrency()->willReturn($usd);
        $channel->getCurrencies()->willReturn(new ArrayCollection([$usd, $eur]));
        $channel->getCode()->willReturn('web');

        $storage->set('_currency_web', 'USD')->shouldNotBeCalled();
        $storage->remove('_currency_web')->shouldBeCalled();

        $this->set($channel, 'USD');
    }

    function it_removes_a_currency_for_a_given_channel_if_it_is_not_available(
        StorageInterface $storage,
        ChannelInterface $channel,
    ): void {
        $usd = new Currency();
        $usd->setCode('USD');

        $eur = new Currency();
        $eur->setCode('EUR');

        $channel->getBaseCurrency()->willReturn($usd);
        $channel->getCurrencies()->willReturn(new ArrayCollection([$usd, $eur]));
        $channel->getCode()->willReturn('web');

        $storage->set('_currency_web', 'GBP')->shouldNotBeCalled();
        $storage->remove('_currency_web')->shouldBeCalled();

        $this->set($channel, 'GBP');
    }
}
