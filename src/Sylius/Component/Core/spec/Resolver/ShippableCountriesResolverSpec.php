<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Resolver\ShippableCountriesResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ShippableCountriesResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShippableCountriesResolver::class);
    }

    function let(RepositoryInterface $countryRepository, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($countryRepository, $channelContext);
    }

    function it_returns_the_channels_shipping_countries(
        ChannelInterface $channel,
        RepositoryInterface $countryRepository,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $firstCountry->isEnabled()->willReturn(true);
        $secondCountry->isEnabled()->willReturn(true);

        $countries = [
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ];

        $countryRepository->findAll()->willReturn($countries);

        $channel->getShippableCountries()->willReturn(new ArrayCollection([
            $firstCountry->getWrappedObject(),
        ]));

        $this($channel)->shouldReturn([
            $firstCountry->getWrappedObject(),
        ]);
    }

    function it_returns_all_enabled_countries_if_the_channel_has_no_shipping_countries_defined(
        ChannelInterface $channel,
        RepositoryInterface $countryRepository,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $firstCountry->isEnabled()->willReturn(true);
        $secondCountry->isEnabled()->willReturn(true);

        $countries = [
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ];

        $countryRepository->findBy(['enabled' => true])->willReturn($countries);

        $channel->getShippableCountries()->willReturn(new ArrayCollection());

        $this($channel)->shouldReturn($countries);
    }
}
