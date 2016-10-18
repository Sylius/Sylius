<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Core\Test\Services\DefaultUnitedStatesChannelFactory;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class DefaultUnitedStatesChannelFactorySpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory
    ) {
        $this->beConstructedWith(
            $channelRepository,
            $countryRepository,
            $currencyRepository,
            $localeRepository,
            $zoneMemberRepository,
            $zoneRepository,
            $channelFactory,
            $countryFactory,
            $currencyFactory,
            $localeFactory,
            $zoneFactory,
            $zoneMemberFactory,
            'USD',
            'en_US'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultUnitedStatesChannelFactory::class);
    }

    function it_implements_a_default_channel_factory_interface()
    {
        $this->shouldImplement(DefaultChannelFactoryInterface::class);
    }

    function it_creates_a_default_united_states_channel_with_country_zone_and_usd_as_default_currency(
        RepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        RepositoryInterface $zoneMemberRepository,
        RepositoryInterface $zoneRepository,
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory,
        ZoneMemberInterface $zoneMember,
        ZoneInterface $zone,
        ChannelInterface $channel,
        CountryInterface $unitedStates,
        CurrencyInterface $currency,
        LocaleInterface $locale
    ) {
        $channel->getName()->willReturn('United States');
        $channelFactory->createNamed('United States')->willReturn($channel);

        $localeFactory->createNew()->willReturn($locale);
        $locale->setCode('en_US')->shouldBeCalled();

        $zoneMemberFactory->createNew()->willReturn($zoneMember);
        $zoneFactory->createNew()->willReturn($zone);

        $channel->setCode('WEB-US')->shouldBeCalled();
        $channel->setTaxCalculationStrategy('order_items_based')->shouldBeCalled();

        $zoneMember->setCode('US')->shouldBeCalled();
        $zone->setCode('US')->shouldBeCalled();
        $zone->setName('United States')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->addMember($zoneMember)->shouldBeCalled();

        $countryFactory->createNew()->willReturn($unitedStates);
        $unitedStates->setCode('US')->shouldBeCalled();

        $currencyFactory->createNew()->willReturn($currency);
        $currency->setCode('USD')->shouldBeCalled();
        $currency->setExchangeRate(1.00)->shouldBeCalled();

        $channel->setDefaultCurrency($currency)->shouldBeCalled();
        $channel->addCurrency($currency)->shouldBeCalled();
        $channel->setDefaultLocale($locale)->shouldBeCalled();
        $channel->addLocale($locale)->shouldBeCalled();

        $currencyRepository->findOneBy(['code' => 'USD'])->willReturn(null);
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn(null);

        $currencyRepository->add($currency)->shouldBeCalled();
        $localeRepository->add($locale)->shouldBeCalled();

        $countryRepository->add($unitedStates)->shouldBeCalled();
        $channelRepository->add($channel)->shouldBeCalled();
        $zoneRepository->add($zone)->shouldBeCalled();
        $zoneMemberRepository->add($zoneMember)->shouldBeCalled();

        $this->create();
    }
}
