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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class TaxonCollectionDataProviderSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($taxonRepository, $userContext);
    }

    function it_supports_only_taxons(): void
    {
        $this->supports(TaxonInterface::class, 'get')->shouldReturn(true);
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [TaxonInterface::class, 'get_from_menu_taxon', []])
        ;
    }

    function it_provides_taxon_from_channel_menu_taxon_if_logged_in_user_is_not_admin(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
        UserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_USER']);

        $channel->getMenuTaxon()->willReturn($menuTaxon);
        $taxonRepository->findChildrenByChannelMenuTaxon($menuTaxon)->willReturn([$firstTaxon, $secondTaxon]);

        $this
            ->getCollection(TaxonInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstTaxon, $secondTaxon])
        ;
    }

    function it_provides_taxon_from_channel_menu_taxon_if_there_is_no_logged_in_user(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel
    ): void {
        $userContext->getUser()->willReturn(null);

        $channel->getMenuTaxon()->willReturn($menuTaxon);
        $taxonRepository->findChildrenByChannelMenuTaxon($menuTaxon)->willReturn([$firstTaxon, $secondTaxon]);

        $this
            ->getCollection(TaxonInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstTaxon, $secondTaxon])
        ;
    }

    function it_provides_all_taxons_if_logged_in_user_is_admin(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
        ChannelInterface $channel,
        UserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $channel->getMenuTaxon()->willReturn($menuTaxon);
        $taxonRepository->findAll()->willReturn([$firstTaxon, $secondTaxon, $thirdTaxon]);

        $this
            ->getCollection(TaxonInterface::class, 'get', [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstTaxon, $secondTaxon, $thirdTaxon])
        ;
    }
}
