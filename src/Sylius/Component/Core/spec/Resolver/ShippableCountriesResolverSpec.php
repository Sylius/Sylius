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
        $firstCountry->getName()->willReturn('Germany');
        $firstCountry->getCode()->willReturn('DE');

        $secondCountry->getName()->willReturn('France');
        $secondCountry->getCode()->willReturn('FR');

        $countries = new ArrayCollection([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);

        $countryRepository->findAll()->willReturn($countries);

        $channel->getShippableCountries()->willReturn(new ArrayCollection([
            $firstCountry->getWrappedObject(),
        ]));

        $this->getShippableCountries($channel)->shouldReturn([
            'Germany' => 'DE',
        ]);
    }

    function it_returns_all_countries_if_the_channel_has_no_shipping_countries_defined(
        ChannelInterface $channel,
        RepositoryInterface $countryRepository,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $firstCountry->getName()->willReturn('Germany');
        $firstCountry->getCode()->willReturn('DE');

        $secondCountry->getName()->willReturn('France');
        $secondCountry->getCode()->willReturn('FR');

        $countries = new ArrayCollection([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);

        $countryRepository->findAll()->willReturn($countries);

        $channel->getShippableCountries()->willReturn(new ArrayCollection());

        $this->getShippableCountries($channel)->shouldReturn([
            'Germany' => 'DE',
            'France' => 'FR',
        ]);
    }

    function it_returns_the_shipping_countries_of_the_current_channel_if_no_channel_is_provided(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        RepositoryInterface $countryRepository,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $firstCountry->getName()->willReturn('Germany');
        $firstCountry->getCode()->willReturn('DE');

        $secondCountry->getName()->willReturn('France');
        $secondCountry->getCode()->willReturn('FR');

        $countries = new ArrayCollection([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);

        $countryRepository->findAll()->willReturn($countries);

        $channel->getShippableCountries()->willReturn(new ArrayCollection([
            $firstCountry->getWrappedObject(),
        ]));

        $this->getShippableCountries()->shouldReturn([
            'Germany' => 'DE',
        ]);
    }
}
