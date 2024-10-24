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

namespace spec\Sylius\Component\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class DefaultChannelFactorySpec extends ObjectBehavior
{
    function let(
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        RepositoryInterface $channelRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
    ): void {
        $this->beConstructedWith(
            $channelFactory,
            $currencyFactory,
            $localeFactory,
            $channelRepository,
            $currencyRepository,
            $localeRepository,
            'en_US',
        );
    }

    function it_implements_a_default_channel_factory_interface(): void
    {
        $this->shouldImplement(DefaultChannelFactoryInterface::class);
    }

    function it_creates_a_default_channel_and_persist_it(
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        RepositoryInterface $channelRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale,
    ): void {
        $localeFactory->createNew()->willReturn($locale);
        $locale->setCode('en_US')->shouldBeCalled();

        $currencyFactory->createNew()->willReturn($currency);
        $currency->setCode('USD')->shouldBeCalled();

        $channelFactory->createNamed('Default')->willReturn($channel);

        $channel->setCode('DEFAULT')->shouldBeCalled();
        $channel->setTaxCalculationStrategy('order_items_based')->shouldBeCalled();

        $channel->addCurrency($currency)->shouldBeCalled();
        $channel->setBaseCurrency($currency)->shouldBeCalled();
        $channel->addLocale($locale)->shouldBeCalled();
        $channel->setDefaultLocale($locale)->shouldBeCalled();
        $channel->getShopBillingData()->willReturn(null);
        $channel->setShopBillingData(new ShopBillingData())->shouldBeCalled();

        $currencyRepository->findOneBy(['code' => 'USD'])->willReturn(null);
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn(null);

        $currencyRepository->add($currency)->shouldBeCalled();
        $localeRepository->add($locale)->shouldBeCalled();
        $channelRepository->add($channel)->shouldBeCalled();

        $this->create()->shouldReturn(['channel' => $channel, 'currency' => $currency, 'locale' => $locale]);
    }
}
