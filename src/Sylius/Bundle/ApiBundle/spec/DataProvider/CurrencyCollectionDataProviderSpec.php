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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class CurrencyCollectionDataProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($currencyRepository, $userContext);
    }

    function it_supports_only_currencies(): void
    {
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
        $this->supports(CurrencyInterface::class, 'get')->shouldReturn(true);
    }

    function it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [CurrencyInterface::class, 'shop_get', []])
        ;
    }

    function it_provides_currencies_available_in_channel_if_user_is_not_admin(
        UserContextInterface $userContext,
        UserInterface $user,
        ChannelInterface $channel,
        CurrencyInterface $firstCurrency,
        CurrencyInterface $secondCurrency
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_USER']);

        $channel->addCurrency($firstCurrency);
        $channel->addCurrency($secondCurrency);

        $channel->getCurrencies()->willReturn(new ArrayCollection([$firstCurrency->getWrappedObject(), $secondCurrency->getWrappedObject()]));

        $this
            ->getCollection(CurrencyInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(new ArrayCollection([$firstCurrency->getWrappedObject(), $secondCurrency->getWrappedObject()]))
        ;
    }

    function it_provides_all_currencies_if_user_is_admin(
        UserContextInterface $userContext,
        AdminUserInterface $user,
        ChannelInterface $channel,
        CurrencyInterface $firstCurrency,
        CurrencyInterface $secondCurrency,
        RepositoryInterface $currencyRepository
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $currencyRepository->findAll()->willReturn([$firstCurrency, $secondCurrency]);

        $this
            ->getCollection(CurrencyInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstCurrency, $secondCurrency])
        ;
    }
}
