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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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

    function it_returns_the_channels_shippable_countries(
        ChannelInterface $channel,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $firstCountry->isEnabled()->willReturn(true);
        $secondCountry->isEnabled()->willReturn(true);

        $channel->getShippableCountries()->willReturn(new ArrayCollection([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]));

        $this($channel)->shouldReturn([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);
    }

    function it_returns_all_enabled_countries_if_the_channel_has_no_enabled_shipping_countries_defined(
        ChannelInterface $channel,
        RepositoryInterface $countryRepository,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry,
        CountryInterface $disabledCountry
    ): void {
        $disabledCountry->isEnabled()->willReturn(false);

        $channel->getShippableCountries()->willReturn(new ArrayCollection([
            $disabledCountry->getWrappedObject(),
        ]));

        $countryRepository->findBy(['enabled' => true])->willReturn([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);

        $this($channel)->shouldReturn([
            $firstCountry->getWrappedObject(),
            $secondCountry->getWrappedObject(),
        ]);
    }
}
