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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class DefaultFranceChannelFactorySpec extends ObjectBehavior
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
            $zoneMemberFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\DefaultFranceChannelFactory');
    }

    function it_implements_default_channel_factory_interface()
    {
        $this->shouldImplement(DefaultChannelFactoryInterface::class);
    }

    function it_creates_default_france_channel_with_country_zone_and_eur_as_default_currency(
        $channelRepository,
        $countryRepository,
        $currencyRepository,
        $localeRepository,
        $zoneMemberRepository,
        $zoneRepository,
        $channelFactory,
        $countryFactory,
        $localeFactory,
        $currencyFactory,
        $zoneMemberFactory,
        $zoneFactory,
        ZoneMemberInterface $zoneMember,
        ZoneInterface $zone,
        ChannelInterface $channel,
        CountryInterface $france,
        CurrencyInterface $euro,
        LocaleInterface $locale
    ) {
        $channel->getName()->willReturn('France');
        $channelFactory->createNamed('France')->willReturn($channel);

        $localeFactory->createNew()->willReturn($locale);

        $zoneMemberFactory->createNew()->willReturn($zoneMember);
        $zoneFactory->createNew()->willReturn($zone);

        $channel->setCode('WEB-FR')->shouldBeCalled();
        $channel->setDefaultTaxCalculationStrategy('order_items_based')->shouldBeCalled();

        $zoneMember->setCode('FR')->shouldBeCalled();
        $zone->setCode('FR')->shouldBeCalled();
        $zone->setName('France')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->addMember($zoneMember)->shouldBeCalled();

        $countryFactory->createNew()->willReturn($france);
        $france->setCode('FR')->shouldBeCalled();

        $currencyFactory->createNew()->willReturn($euro);
        $euro->setCode('EUR')->shouldBeCalled();
        $euro->setExchangeRate(1.00)->shouldBeCalled();
        $euro->setBase(true)->shouldBeCalled();

        $channel->setDefaultCurrency($euro)->shouldBeCalled();
        $channel->setDefaultLocale($locale)->shouldBeCalled();
        $channel->addLocale($locale)->shouldBeCalled();

        $currencyRepository->add($euro)->shouldBeCalled();
        $countryRepository->add($france)->shouldBeCalled();

        $channelRepository->add($channel)->shouldBeCalled();
        $zoneRepository->add($zone)->shouldBeCalled();
        $zoneMemberRepository->add($zoneMember)->shouldBeCalled();
        $localeRepository->add($locale)->shouldBeCalled();

        $this->create();
    }
}
