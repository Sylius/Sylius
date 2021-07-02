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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class LocaleCollectionDataProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($localeRepository, $userContext);
    }

    function it_supports_only_locales(): void
    {
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
        $this->supports(LocaleInterface::class, 'get')->shouldReturn(true);
    }

    function it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [LocaleInterface::class, 'shop_get', []])
        ;
    }

    function it_provides_locales_available_in_channel_if_user_is_not_admin(
        UserContextInterface $userContext,
        UserInterface $user,
        ChannelInterface $channel,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_USER']);

        $channel->addLocale($firstLocale);
        $channel->addLocale($secondLocale);

        $channel->getLocales()->willReturn(new ArrayCollection([$firstLocale->getWrappedObject(), $secondLocale->getWrappedObject()]));

        $this
            ->getCollection(LocaleInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(new ArrayCollection([$firstLocale->getWrappedObject(), $secondLocale->getWrappedObject()]))
        ;
    }

    function it_provides_all_locales_if_user_is_admin(
        UserContextInterface $userContext,
        AdminUserInterface $user,
        ChannelInterface $channel,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale,
        RepositoryInterface $localeRepository
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $localeRepository->findAll()->willReturn([$firstLocale, $secondLocale]);

        $this
            ->getCollection(LocaleInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstLocale, $secondLocale])
        ;
    }
}
