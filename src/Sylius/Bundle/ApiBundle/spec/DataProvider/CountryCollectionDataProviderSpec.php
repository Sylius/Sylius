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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class CountryCollectionDataProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($countryRepository, $userContext);
    }

    function it_supports_only_countries(): void
    {
        $this->supports(CountryInterface::class, 'get')->shouldReturn(true);
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_countries_from_channel_if_logged_in_user_is_not_admin_user(
        UserContextInterface $userContext,
        UserInterface $user,
        ChannelInterface $channel,
        CountryInterface $country
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_USER']);

        $channel->getCountries()->willReturn(new ArrayCollection([$country->getWrappedObject()]));

        $this
            ->getCollection(CountryInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(new ArrayCollection([$country->getWrappedObject()]))
        ;
    }

    function it_provides_countries_from_channel_if_there_is_no_logged_in_user(
        UserContextInterface $userContext,
        ChannelInterface $channel,
        CountryInterface $country
    ): void {
        $userContext->getUser()->willReturn(null);

        $channel->getCountries()->willReturn(new ArrayCollection([$country->getWrappedObject()]));

        $this
            ->getCollection(CountryInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(new ArrayCollection([$country->getWrappedObject()]))
        ;
    }

    function it_provides_all_countries_if_channel_has_no_associated_countries_and_there_is_no_logged_in_user(
        RepositoryInterface $countryRepository,
        UserContextInterface $userContext,
        ChannelInterface $channel,
        CountryInterface $country
    ): void {
        $userContext->getUser()->willReturn(null);

        $channel->getCountries()->willReturn(new ArrayCollection());

        $countryRepository->findAll()->willReturn([$country]);

        $this
            ->getCollection(CountryInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$country])
        ;
    }

    function it_provides_all_countries_if_there_is_no_channel_in_context(
        RepositoryInterface $countryRepository,
        UserContextInterface $userContext,
        CountryInterface $country
    ): void {
        $userContext->getUser()->willReturn(null);

        $countryRepository->findAll()->willReturn([$country]);

        $this
            ->getCollection(CountryInterface::class, 'get', [])
            ->shouldReturn([$country])
        ;
    }

    function it_provides_all_countries_if_logged_in_user_is_an_admin_user(
        RepositoryInterface $countryRepository,
        UserContextInterface $userContext,
        AdminUserInterface $user,
        ChannelInterface $channel,
        CountryInterface $country
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $countryRepository->findAll()->willReturn([$country]);

        $this
            ->getCollection(CountryInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$country])
        ;
    }
}
